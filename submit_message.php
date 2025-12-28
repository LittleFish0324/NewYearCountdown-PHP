<?php
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

// 获取用户IP
$user_ip = $_SERVER['REMOTE_ADDR'];

// 获取表单数据
$nickname = $_POST['nickname'] ?? '';
$content = $_POST['content'] ?? '';

// 验证数据
if (empty($nickname) || empty($content)) {
    die(json_encode(['success' => false, 'message' => '请填写完整信息']));
}

// 限制消息长度
if (mb_strlen($content) > 500) {
    die(json_encode(['success' => false, 'message' => '祝福内容不能超过500个字符']));
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

if (isLowQualityContent($content)) {
    die(json_encode(['success' => false, 'message' => '留言内容质量过低，请输入有意义的内容！']));
}

// 检查IP在5小时内的提交次数
$five_hours_ago = date('Y-m-d H:i:s', strtotime('-5 hours'));
$stmt = $conn->prepare("SELECT COUNT(*) FROM message WHERE ip = ? AND created_at >= ?");
$stmt->bind_param("ss", $user_ip, $five_hours_ago);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count >= 5) {
    die(json_encode(['success' => false, 'message' => '同一个IP在5小时内只能发布5次祝福']));
}

// 检查上次提交时间（2分钟间隔）
$stmt = $conn->prepare("SELECT created_at FROM message WHERE ip = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("s", $user_ip);
$stmt->execute();
$stmt->bind_result($last_time);
$stmt->fetch();
$stmt->close();

if ($last_time) {
    $two_minutes_ago = strtotime('-2 minutes');
    if (strtotime($last_time) > $two_minutes_ago) {
        die(json_encode(['success' => false, 'message' => '请间隔2分钟后再发布祝福']));
    }
}

// 插入留言
$stmt = $conn->prepare("INSERT INTO message (nickname, content, ip) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nickname, $content, $user_ip);

if ($stmt->execute()) {
    // 返回完整消息格式
    $full_message = $nickname . '：' . $content;
    die(json_encode(['success' => true, 'message' => $full_message]));
} else {
    die(json_encode(['success' => false, 'message' => '发布失败，请稍后重试']));
}

$stmt->close();
$conn->close();
?>