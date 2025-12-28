<?php
// 设置响应头
header('Content-Type: application/json');

// 直接定义数据库配置
$servername = "localhost";
$username = "newyearcountdown";
$password = "NPcm7A9wiGYiSfdT";
$dbname = "newyearcountdown";

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => '数据库连接失败']));
}

// 创建留言表（如果不存在） - 与实际表结构匹配
$create_message_table = "CREATE TABLE IF NOT EXISTS message (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nickname VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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

// 低质量内容检测
function isLowQualityContent($content) {
    // 规则1：内容过短且无意义（纯数字、纯符号、重复字符等）
    $cleanContent = preg_replace('/\s+/', '', $content);
    $length = mb_strlen($cleanContent);
    
    // 空内容已在前面验证
    
    // 规则2：纯数字且长度小于5
    if (ctype_digit($cleanContent) && $length < 5) {
        return true;
    }
    
    // 规则3：字符重复率过高（超过80%为同一字符）
    if ($length > 1) {
        $charCounts = count_chars($cleanContent, 1);
        $maxCount = max($charCounts);
        $repeatRatio = $maxCount / $length;
        if ($repeatRatio > 0.8) {
            return true;
        }
    }
    
    // 规则4：包含常见的无意义字符串
    $meaninglessPatterns = [
        '/^\d{1,4}$/', // 1-4位纯数字
        '/^[a-zA-Z]{1,4}$/', // 1-4位纯字母（不含中文）
        '/^[\pP\pS]+$/', // 纯标点符号
        '/^(\w)\1+$/', // 单一字符重复
        '/^(\w{1,2})\1+$/', // 两字符重复
        '/^xxx+$/', // xxx重复
        '/^\.\.\.+$/', // 连续点
    ];
    
    foreach ($meaninglessPatterns as $pattern) {
        if (preg_match($pattern, $cleanContent)) {
            return true;
        }
    }
    
    // 规则5：内容长度过短（少于4个字符）
    if ($length < 4) {
        return true;
    }
    
    return false;
}

// 检测IP频率限制（5小时内最多5次，每次间隔5分钟）
function check_ip_limit($ip) {
    global $conn;
    
    // 检查5小时内的发布次数
    $five_hours_ago = date('Y-m-d H:i:s', strtotime('-5 hours'));
    $sql = "SELECT COUNT(*) as count FROM message WHERE create_time >= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $five_hours_ago);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    
    if ($count >= 5) {
        return ['success' => false, 'message' => '同一个IP在5小时内只能发布5次祝福'];
    }
    
    // 检查上次发布时间（间隔5分钟）
    $sql = "SELECT create_time FROM message ORDER BY create_time DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($last_time);
    $stmt->fetch();
    $stmt->close();
    
    if ($last_time) {
        $five_minutes_ago = strtotime('-5 minutes');
        if (strtotime($last_time) > $five_minutes_ago) {
            $remaining_time = 300 - (time() - strtotime($last_time));
            return ['success' => false, 'message' => '请间隔5分钟后再发布祝福，还需等待' . $remaining_time . '秒'];
        }
    }
    
    return ['success' => true];
}

// 处理GET请求 - 获取留言列表
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 分页参数处理
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $page = max(1, $page); // 确保页码至少为1
    $limit = 40; // 每页最多40条留言
    $offset = ($page - 1) * $limit;
    
    // 筛选参数
    $nickname = isset($_GET['nickname']) ? trim($_GET['nickname']) : '';
    $content = isset($_GET['content']) ? trim($_GET['content']) : '';
    $date = isset($_GET['date']) ? trim($_GET['date']) : '';
    
    // 构建SQL查询
    $sql = "SELECT * FROM message WHERE 1=1";
    $sql_total = "SELECT COUNT(*) as total FROM message WHERE 1=1";
    
    // 添加筛选条件
    $where_clauses = [];
    $query_params = [];
    
    if (!empty($nickname)) {
        $where_clauses[] = "nickname LIKE '%" . $conn->real_escape_string($nickname) . "%'";
    }
    
    if (!empty($content)) {
        $where_clauses[] = "content LIKE '%" . $conn->real_escape_string($content) . "%'";
    }
    
    if (!empty($date)) {
        if (strpos($date, '至') !== false) {
            // 日期范围格式：YYYY-MM-DD至YYYY-MM-DD
            list($start_date, $end_date) = explode('至', $date);
            $start_date = trim($start_date);
            $end_date = trim($end_date);
            $where_clauses[] = "create_time BETWEEN '" . $conn->real_escape_string($start_date) . " 00:00:00' AND '" . $conn->real_escape_string($end_date) . " 23:59:59'";
        } else {
            // 具体日期格式：YYYY-MM-DD
            $where_clauses[] = "DATE(create_time) = '" . $conn->real_escape_string($date) . "'";
        }
    }
    
    // 合并where子句
    if (!empty($where_clauses)) {
        $where_sql = " AND " . implode(" AND ", $where_clauses);
        $sql .= $where_sql;
        $sql_total .= $where_sql;
    }
    
    // 添加排序和分页
    $sql .= " ORDER BY create_time DESC LIMIT " . $limit . " OFFSET " . $offset;
    
    // 执行查询获取留言列表
    $result = $conn->query($sql);
    $messages = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // 将create_time字段重命名为created_at，以便前端代码能够正确处理
            $row['created_at'] = $row['create_time'];
            unset($row['create_time']);
            $messages[] = $row;
        }
        $result->close();
    }
    
    // 获取总留言数
    $result_total = $conn->query($sql_total);
    $total = 0;
    if ($result_total) {
        $row_total = $result_total->fetch_assoc();
        $total = $row_total['total'];
        $result_total->close();
    }
    
    // 计算总页数
    $total_pages = ceil($total / $limit);
    
    // 返回结果
    echo json_encode([
        'success' => true, 
        'messages' => $messages, 
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => $total_pages,
        'filters' => [
            'nickname' => $nickname,
            'content' => $content,
            'date' => $date
        ]
    ]);
}
// 处理POST请求 - 发布留言
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    
    // 检测低质量内容
    if (isLowQualityContent($content)) {
        echo json_encode(['success' => false, 'message' => '留言内容质量过低，请输入有意义的内容！']);
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
    $sql = "INSERT INTO message (nickname, content) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nickname, $filtered_content);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => '留言发布成功']);
    } else {
        echo json_encode(['success' => false, 'message' => '留言发布失败，请稍后重试']);
        exit;
    }
    
    $stmt->close();
}

// 关闭连接
$conn->close();
?>