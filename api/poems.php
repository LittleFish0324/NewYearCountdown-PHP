<?php
// 设置响应头
header('Content-Type: application/json');

// 古诗文件路径
$file_path = '../word/AncientPoetry.txt';

// 检查文件是否存在
if (!file_exists($file_path)) {
    die(json_encode(['success' => false, 'message' => '古诗文件不存在']));
}

// 读取文件内容
$content = file_get_contents($file_path);

// 将内容按行分割
$lines = explode(PHP_EOL, $content);

// 过滤空行
$poems = array_filter($lines, function($line) {
    return trim($line) !== '';
});

// 返回JSON响应
echo json_encode([
    'success' => true,
    'poems' => array_values($poems)
]);
?>