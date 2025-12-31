<?php
header('Content-Type: application/json');

// 引入配置文件
require_once 'config.php';

// 获取数据库连接
$conn = getDbConnection();

// 创建必要的数据库和表
createDatabaseAndTables($conn);

// 获取最近的留言
$stmt = $conn->prepare("SELECT nickname, content FROM message ORDER BY create_time DESC LIMIT 50");
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row['nickname'] . '：' . $row['content'];
}

// 添加一些默认的新年祝福
$default_messages = [
    '新年快乐！',
    '万事如意！',
    '身体健康！',
    '恭喜发财！',
    '阖家欢乐！',
    '心想事成！',
    '步步高升！',
    '财源广进！',
    '福星高照！',
    '吉祥如意！'
];

// 合并默认消息和用户消息
$all_messages = array_merge($messages, $default_messages);

// 随机排序
shuffle($all_messages);

die(json_encode($all_messages));

$stmt->close();
$conn->close();
?>