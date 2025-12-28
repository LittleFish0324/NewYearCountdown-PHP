<?php
// 设置响应头
header('Content-Type: application/json');

// 数据库配置
include '../config.php';

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => '数据库连接失败']));
}

// 创建留言表（如果不存在）
$create_message_table = "CREATE TABLE IF NOT EXISTS messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nickname VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    ip VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$conn->query($create_message_table);

// 违禁词列表
$forbidden_words = [
    // 政治敏感词
    '政治', '共产党', '政府', '习近平', '李克强', '毛泽东', 
    '天安门', '中南海', '国务院', '外交部', '国防部', 
    
    // 色情低俗词
    '色情', '黄色', '成人', '性爱', '妓女', '嫖客', 
    '裸照', '三级片', 'AV', '色情网站', '黄色网站',
    
    // 暴力恐怖词
    '暴力', '恐怖', '杀人', '抢劫', '爆炸', '毒品',
    '枪支', '炸弹', '自杀', '自残', '恐怖分子',
    
    // 赌博博彩词
    '赌博', '博彩', '彩票', '赌场', '赌球', '六合彩',
    '时时彩', '赌钱', '下注', '赔率', '开奖',
    
    // 广告推广词
    '广告', '推广', '营销', '代购', '微商', '加盟',
    '赚钱', '兼职', '创业', '投资', '理财',
    
    // 其他违规词
    '黑客', '病毒', '破解', '翻墙', 'VPN', '盗版',
    '假货', '诈骗', '骗人', '坑人', '垃圾', '废物'
];

// 违禁词过滤函数
function filter_forbidden_words($text) {
    global $forbidden_words;
    $filtered_text = $text;
    foreach ($forbidden_words as $word) {
        $filtered_text = str_replace($word, str_repeat('*', mb_strlen($word)), $filtered_text);
    }
    return $filtered_text;
}

// 检测文本中是否包含违禁词
function contains_forbidden_words($text) {
    global $forbidden_words;
    foreach ($forbidden_words as $word) {
        if (strpos($text, $word) !== false) {
            return true;
        }
    }
    return false;
}

// 检测IP频率限制（5小时内最多5次，每次间隔2分钟）
function check_ip_limit($ip) {
    global $conn;
    
    // 检查5小时内的发布次数
    $five_hours_ago = date('Y-m-d H:i:s', strtotime('-5 hours'));
    $sql = "SELECT COUNT(*) as count FROM messages WHERE ip = ? AND created_at >= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $ip, $five_hours_ago);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    
    if ($count >= 5) {
        return ['success' => false, 'message' => '同一个IP在5小时内只能发布5次祝福'];
    }
    
    // 检查上次发布时间（间隔2分钟）
    $sql = "SELECT created_at FROM messages WHERE ip = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->bind_result($last_time);
    $stmt->fetch();
    $stmt->close();
    
    if ($last_time) {
        $two_minutes_ago = strtotime('-2 minutes');
        if (strtotime($last_time) > $two_minutes_ago) {
            $remaining_time = 120 - (time() - strtotime($last_time));
            return ['success' => false, 'message' => '请间隔2分钟后再发布祝福，还需等待' . $remaining_time . '秒'];
        }
    }
    
    return ['success' => true];
}

// 处理GET请求 - 获取留言列表
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $show_all = isset($_GET['showAll']) && $_GET['showAll'] == 1;
    
    if ($show_all) {
        $sql = "SELECT * FROM messages ORDER BY created_at DESC";
    } else {
        $sql = "SELECT * FROM messages ORDER BY created_at DESC LIMIT 3";
    }
    
    $result = $conn->query($sql);
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    // 获取总留言数
    $sql_total = "SELECT COUNT(*) as total FROM messages";
    $result_total = $conn->query($sql_total);
    $row_total = $result_total->fetch_assoc();
    $total = $row_total['total'];
    
    echo json_encode(['success' => true, 'messages' => $messages, 'total' => $total]);
}
// 处理POST请求 - 发布留言
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取用户IP
    $user_ip = $_SERVER['REMOTE_ADDR'];
    
    // 检查IP频率限制
    $ip_check = check_ip_limit($user_ip);
    if (!$ip_check['success']) {
        echo json_encode($ip_check);
        exit;
    }
    
    // 获取表单数据
    $nickname = $_POST['nickname'] ?? '';
    $content = $_POST['content'] ?? '';
    
    // 验证数据
    if (empty($nickname) || empty($content)) {
        echo json_encode(['success' => false, 'message' => '昵称和留言内容不能为空']);
        exit;
    }
    
    // 验证昵称长度
    if (mb_strlen($nickname) > 50) {
        echo json_encode(['success' => false, 'message' => '昵称不能超过50个字符']);
        exit;
    }
    
    // 验证留言长度
    if (mb_strlen($content) > 500) {
        echo json_encode(['success' => false, 'message' => '留言内容不能超过500个字符']);
        exit;
    }
    
    // 检测URL
    $url_pattern = '/(https?:\/\/|www\.|([a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?)|(\d+\.\d+\.\d+\.\d+(:\d+)?))(\/[^\s]*)?/i';
    if (preg_match($url_pattern, $content)) {
        echo json_encode(['success' => false, 'message' => '留言内容不能包含任何形式的链接']);
        exit;
    }
    
    // 检测违禁词
    if (contains_forbidden_words($content)) {
        echo json_encode(['success' => false, 'message' => '留言内容包含违禁词，请修改后重新提交']);
        exit;
    }
    
    // 过滤违禁词
    $filtered_content = filter_forbidden_words($content);
    
    // 插入留言
    $sql = "INSERT INTO messages (nickname, content, ip) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nickname, $filtered_content, $user_ip);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => '留言发布成功']);
    } else {
        echo json_encode(['success' => false, 'message' => '留言发布失败，请稍后重试']);
    }
    
    $stmt->close();
}

// 关闭连接
$conn->close();
?>