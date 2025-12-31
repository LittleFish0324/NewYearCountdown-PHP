<?php
// 设置响应头
header('Content-Type: application/json');

// 引入配置文件
require_once '../config.php';

// 获取数据库连接
$conn = getDbConnection();

// 创建必要的数据库和表
createDatabaseAndTables($conn);

// 加载敏感词
function loadSensitiveWords() {
    $banword_dir = '../banword';
    $sensitive_words = [];
    
    if (is_dir($banword_dir)) {
        $files = scandir($banword_dir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
                $file_path = $banword_dir . '/' . $file;
                $words = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $sensitive_words = array_merge($sensitive_words, $words);
            }
        }
    }
    
    // 移除空词和重复词
    $sensitive_words = array_unique(array_filter(array_map('trim', $sensitive_words)));
    // 按长度降序排序，优先匹配长词
    usort($sensitive_words, function($a, $b) {
        return mb_strlen($b) - mb_strlen($a);
    });
    
    return $sensitive_words;
}

// 违禁词过滤函数
function filter_forbidden_words($text, $sensitive_words = null) {
    if ($sensitive_words === null) {
        $sensitive_words = loadSensitiveWords();
    }
    
    $filtered_text = $text;
    foreach ($sensitive_words as $word) {
        $filtered_text = str_replace($word, str_repeat('*', mb_strlen($word)), $filtered_text);
    }
    return $filtered_text;
}

// 检测文本中是否包含违禁词，返回第一个匹配的敏感词
function contains_forbidden_words($text, $sensitive_words = null) {
    if ($sensitive_words === null) {
        $sensitive_words = loadSensitiveWords();
    }
    
    foreach ($sensitive_words as $word) {
        if (mb_strpos($text, $word) !== false) {
            return $word;
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

// 检测用户频率限制（5小时内最多5次，每次间隔5分钟）
function check_user_limit($user_id) {
    global $conn;
    
    // 检查5小时内的发布次数
    $five_hours_ago = date('Y-m-d H:i:s', strtotime('-5 hours'));
    $sql = "SELECT COUNT(*) as count FROM message WHERE user_id = ? AND create_time >= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_id, $five_hours_ago);
    $stmt->execute();
    $count = 0;
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    
    if ($count >= 5) {
        return ['success' => false, 'message' => '同一个用户在5小时内只能发布5次祝福'];
    }
    
    // 检查上次发布时间（间隔5分钟）
    $sql = "SELECT create_time FROM message WHERE user_id = ? ORDER BY create_time DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $last_time = null;
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
    
    // 获取用户唯一标识（IP + User-Agent的组合，解决同一内网下不同设备的问题）
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    // 使用MD5生成唯一的user_id，避免存储明文IP和User-Agent
    $user_id = md5($user_ip . $user_agent);
    
    // 检测用户频率限制（5小时内最多5次，每次间隔5分钟）
    $limit_result = check_user_limit($user_id);
    if (!$limit_result['success']) {
        echo json_encode($limit_result);
        exit;
    }
    
    // 加载敏感词（只加载一次，提高性能）
    $sensitive_words = loadSensitiveWords();
    
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
    
    // 检测昵称是否包含敏感词
    $bad_word = contains_forbidden_words($nickname, $sensitive_words);
    if ($bad_word) {
        echo json_encode(['success' => false, 'message' => '昵称包含敏感词：' . $bad_word]);
        exit;
    }
    
    // 检测内容是否包含敏感词
    $bad_word = contains_forbidden_words($content, $sensitive_words);
    if ($bad_word) {
        echo json_encode(['success' => false, 'message' => '留言内容包含敏感词：' . $bad_word]);
        exit;
    }
    
    // 过滤违禁词
    $filtered_content = filter_forbidden_words($content, $sensitive_words);
    
    // 插入留言，包含user_id
    $sql = "INSERT INTO message (nickname, content, user_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nickname, $filtered_content, $user_id);
    
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