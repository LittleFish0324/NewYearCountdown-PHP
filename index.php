<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    
    <title>2026新年倒计时</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Microsoft YaHei', 'Heiti SC', sans-serif; min-height: 100vh; color: #fff; position: relative; }
        
        /* 背景图片容器 - 轮换功能 */
        .bg-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            filter: blur(5px); /* 雾化效果 */
        }
        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }
        .bg-image.active {
            opacity: 1;
        }
        /* 背景遮罩 - 保证文字可读性 */
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.35);
            z-index: -1;
        }
        /* 确保1.jpg背景图片优先显示 */
        .bg-image:first-child {
            background-image: url('1.jpg');
            z-index: 1;
        }

        /* 漂浮泡泡样式 */
        .bubble-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9;
            overflow: hidden;
        }

        .bubble {
            position: absolute;
            display: inline-block;
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border-radius: 25px;
            font-size: 0.9rem;
            color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: float 8s ease-in-out infinite;
            white-space: normal;
            max-width: 250px;
            word-wrap: break-word;
            opacity: 0.85;
            z-index: 9;
        }

        .bubble::before {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 8px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) translateX(0) scale(0.9);
                opacity: 0;
            }
            10% {
                opacity: 0.85;
            }
            90% {
                opacity: 0.85;
            }
            100% {
                transform: translateY(-150px) translateX(80px) scale(1.05);
                opacity: 0;
            }
        }

        /* 不同大小和动画延迟的泡泡 */
        .bubble:nth-child(3n) {
            animation-delay: 0s;
            background: rgba(255, 215, 0, 0.3);
        }

        .bubble:nth-child(3n+1) {
            animation-delay: 2s;
            background: rgba(255, 105, 180, 0.3);
        }

        .bubble:nth-child(3n+2) {
            animation-delay: 4s;
            background: rgba(70, 130, 180, 0.3);
        }

        .bubble:nth-child(even) {
            animation-duration: 8s;
        }

        .bubble:nth-child(odd) {
            animation-duration: 10s;
        }

        /* 留言板区域美化 */
        .message-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 30px;
            margin: 40px auto;
            max-width: 800px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .message-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 25px;
            color: #fff;
            text-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
        }

        .message-form {
            background: rgba(255, 255, 255, 0.15);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #fff;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ffd700;
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
        }

        #submit-btn {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            color: #333;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        #submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
        }

        #submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .message-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .message-item {
            background: rgba(255, 255, 255, 0.15);
            padding: 20px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.95);
            transition: all 0.3s ease-in-out;
        }

        .message-nickname {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .message-nickname span:first-child {
            font-weight: bold;
            color: #ffd700;
        }

        .message-time {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .message-content {
            line-height: 1.5;
            color: #fff;
        }

        .message-item.full-width {
            grid-column: 1 / -1;
        }

        #load-more {
            text-align: center;
            margin-top: 30px;
        }

        #load-more-btn {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 10px 25px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        #load-more-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .error-message {
            background: rgba(255, 105, 97, 0.3);
            color: #fff;
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
            border: 1px solid rgba(255, 105, 97, 0.5);
        }

        /* 移动端适配 */
        @media (max-width: 768px) {
            .message-section {
                padding: 20px;
                margin: 20px auto;
            }

            .message-title {
                font-size: 1.6rem;
            }

            .message-list {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .message-form {
                padding: 20px;
            }

            .bubble {
                font-size: 0.8rem;
                padding: 8px 12px;
            }
        }

        /* 泡泡特效控制按钮样式 */
        .bubble-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999999;
            display: flex;
            gap: 10px;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .bubble-btn {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .bubble-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .bubble-btn:active {
            transform: translateY(0);
        }

        /* 关闭时的按钮样式 */
        .bubble-btn.off {
            background: rgba(255, 105, 97, 0.3);
            border-color: rgba(255, 105, 97, 0.5);
        }

        /* 移动端按钮样式 */
        @media (max-width: 768px) {
            .bubble-controls {
                top: 10px;
                right: 10px;
            }
            
            .bubble-btn {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
        }

        /* 设备时钟对比样式 */
        .clock-diff {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            padding: 15px 25px;
            border-radius: 50px;
            text-align: center;
            margin: 20px auto 0;
            max-width: 600px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 100;
        }
        
        .clock-diff:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        .clock-diff.hidden {
            display: none;
        }
        
        .clock-diff .accurate {
            color: #00ff88;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .clock-diff .inaccurate {
            color: #ffdd55;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .container { max-width: 100%; width: 100%; margin: 0 auto; padding: 20px; position: relative; z-index: 1; }
        header { text-align: center; padding: 20px 0 20px; }
        h1 { font-size: 3.2rem; margin-bottom: 15px; text-shadow: 0 3px 15px rgba(0,0,0,0.3); letter-spacing: 2px; }
        header p { font-size: 1.2rem; opacity: 0.9; text-shadow: 0 2px 8px rgba(0,0,0,0.2); }

        /* 优化后的倒计时区域（桌面端） */
        .countdowns {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin: 30px 0 50px;
            transition: margin-top 1s ease-in-out;
        }
        
        /* 倒计时结束后的布局变化 */
        .countdowns.countdown-ended {
            margin-top: 180px; /* 增加下移距离，为庆祝文字腾出更多空间 */
        }
        
        /* 庆祝文字样式 */
        .celebration-text {
            position: fixed;
            top: 28%; /* 调整top值，使其往上移动，避免与倒计时卡片过于贴近 */
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.8rem; /* 适当减小字体大小 */
            font-weight: bold;
            color: #fff;
            text-align: center;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.8), 0 0 20px rgba(255, 210, 102, 0.8);
            z-index: 10000;
            background: rgba(0, 0, 0, 0.5);
            padding: 25px 40px; /* 调整内边距，减小整体高度 */
            border-radius: 20px;
            box-shadow: 0 0 30px rgba(255, 210, 102, 0.5);
            opacity: 0;
            animation: celebrationFadeIn 1s ease-in-out forwards;
            max-width: 80%; /* 减小最大宽度，避免文字过长 */
            word-wrap: break-word; /* 确保长文字能正常换行 */
            white-space: pre-wrap; /* 允许正常的换行行为 */
            line-height: 1.5; /* 调整行高，确保文字紧凑显示 */
        }
        
        /* 移动端优化庆祝文字样式 */
        @media (max-width: 768px) {
            .celebration-text {
                font-size: 1.5rem; /* 减小字体大小 */
                padding: 20px 30px; /* 调整内边距 */
                line-height: 1.6; /* 增加行高，提高可读性 */
            }
        }
        
        /* 小屏手机进一步优化 */
        @media (max-width: 480px) {
            .celebration-text {
                font-size: 1.2rem; /* 进一步减小字体大小 */
                padding: 15px 20px; /* 进一步调整内边距 */
                top: 35%; /* 调整位置 */
            }
        }
        
        @keyframes celebrationFadeIn {
            from { opacity: 0; transform: translate(-50%, -60%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }
        .countdown-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            padding: 25px 35px;
            border-radius: 20px;
            text-align: center;
            min-width: 280px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        .countdown-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #ff4d4f, #ff9800); /* 新年红橙渐变 */
        }
        .countdown-title {
            font-size: 1.6rem;
            margin-bottom: 25px;
            color: #fff;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        /* 桌面端倒计时时间布局（一行显示） */
        .countdown-time {
            display: flex;
            justify-content: center;
            gap: 15px;
            align-items: center;
        }
        .time-item {
            background: rgba(255, 255, 255, 0.2);
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 2.8rem;
            font-weight: bold;
            color: #fff;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .time-item:hover {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.3);
        }
        .countdown-time span {
            font-size: 2rem;
            color: #fff;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        /* 祝福按钮区域 */
        .bless-section { text-align: center; margin: 30px 0 60px; }
        #bless-btn {
            background: linear-gradient(90deg, #ffd166, #ff9a8b);
            color: #333;
            border: none;
            padding: 16px 45px;
            font-size: 1.3rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(255,209,102,0.5);
            font-weight: 600;
            letter-spacing: 1px;
        }
        #bless-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }
        #bless-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255,209,102,0.6);
        }
        #bless-count {
            margin-top: 18px;
            font-size: 1.2rem;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* 留言板区域 */
        .message-section {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 35px;
            border-radius: 20px;
            margin-top: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            max-width: 80%; /* 修复：调整为80%宽度 */
            width: 80%; /* 修复：调整为80%宽度 */
            margin-left: auto; /* 保持居中 */
            margin-right: auto; /* 保持居中 */
        }
        .message-title {
            font-size: 1.9rem;
            margin-bottom: 25px;
            border-bottom: 2px solid rgba(255,255,255,0.3);
            padding-bottom: 12px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .message-form { margin-bottom: 35px; }
        .form-group { margin-bottom: 20px; }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 1.2rem;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        /* 优化输入框样式（文字可见） */
        input, textarea {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            resize: none;
            color: #333; /* 输入文字深灰色 */
            background: rgba(255,255,255,0.95); /* 输入框纯白背景 */
            box-shadow: 0 3px 8px rgba(0,0,0,0.08);
            transition: box-shadow 0.3s ease;
        }
        input:focus, textarea:focus {
            outline: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        input::placeholder, textarea::placeholder {
            color: #999; /* placeholder 浅灰色 */
        }

        #submit-btn {
            background: linear-gradient(90deg, #4ecdc4, #3dbbba);
            color: #fff;
            padding: 14px 35px;
            border: none;
            border-radius: 10px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(78,205,196,0.3);
        }
        #submit-btn:hover {
            background: linear-gradient(90deg, #3dbbba, #2daaa9);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(78,205,196,0.4);
        }

        .message-list { 
            margin-top: 25px; 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* 使用更小的列宽最小值，支持更多列 */
            grid-gap: 20px; 
            justify-content: center; 
        }

        /* 优化留言项样式（文字可见） */
        .message-item {
            background: rgba(255,255,255,0.95); /* 留言项纯白背景 */
            color: #333; /* 留言文字深灰色 */
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 0;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        /* 独占一行的留言样式 */
        .message-item.full-width {
            grid-column: 1 / -1; /* 让留言独占一行 */
            max-width: none; /* 修复：移除最大宽度限制，让留言完全填充容器 */
            width: 100%; /* 确保宽度100% */
        }

        /* 烟花特效样式 */
        #fireworks {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9999;
            background: transparent;
        }

        /* 测试按钮样式 */
        .test-section {
            text-align: center;
            margin: 30px 0;
        }

        #test-countdown-btn {
            background: linear-gradient(90deg, #ff6b6b, #ffa502);
            color: #fff;
            border: none;
            padding: 14px 35px;
            font-size: 1.1rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(255,107,107,0.5);
            font-weight: 600;
            letter-spacing: 1px;
        }

        #test-countdown-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255,107,107,0.6);
        }
        .message-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.12);
        }
        .message-nickname {
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            color: #222; /* 昵称深黑色 */
            font-size: 1.1rem;
        }
        .message-time {
            font-size: 0.9rem;
            opacity: 0.7;
            color: #666; /* 时间中灰色 */
        }
        .message-content {
            line-height: 1.8;
            color: #444; /* 留言内容中深灰色 */
            font-size: 1.05rem;
        }

        #load-more { text-align: center; margin-top: 30px; }
        #load-more-btn {
            background: transparent;
            border: 2px solid #fff;
            color: #fff;
            padding: 12px 35px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.1rem;
        }
        #load-more-btn:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }
        .error-message {
            color: #ffeeee;
            background: rgba(255,99,71,0.4);
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
            font-size: 1.1rem;
            text-align: center;
        }

        /* 响应式适配（核心修改：手机端倒计时分行） */
        @media (max-width: 768px) {
            h1 { font-size: 2.5rem; }
            header { padding: 30px 0 15px; }
            header p { font-size: 1.1rem; }

            /* 手机端倒计时卡片宽度自适应 */
            .countdown-card {
                min-width: 100%;
                padding: 20px 15px;
                margin-bottom: 20px;
            }
            .countdown-title { font-size: 1.4rem; margin-bottom: 20px; }

            /* 手机端倒计时：两行布局（天 时 / 分 秒） */
            .countdown-time {
                display: grid;
                grid-template-columns: repeat(2, 1fr); /* 2列布局 */
                gap: 15px 20px; /* 上下间距15px，左右间距20px */
                padding: 0 10px;
            }

            /* 手机端倒计时数字和单位优化 */
            .time-item {
                font-size: 2rem;
                padding: 10px;
                height: 60px;
            }
            .countdown-time span {
                font-size: 1.4rem;
                margin-left: 8px;
            }

            .bless-section { margin: 20px 0 40px; }
            #bless-btn {
                padding: 14px 35px;
                font-size: 1.2rem;
            }

            .message-section {
                padding: 25px 15px;
                max-width: 95%; /* 手机端使用更宽的宽度 */
                width: 95%; /* 手机端使用更宽的宽度 */
            }
            .message-title { font-size: 1.7rem; }
            .form-group { margin-bottom: 15px; }
            input, textarea { padding: 12px; font-size: 1rem; }
            #submit-btn { padding: 12px 25px; font-size: 1.1rem; }

            /* 移动端恢复单列布局 */
            .message-list {
                display: block;
                margin-top: 20px;
            }
            .message-item { 
                padding: 15px; 
                margin-bottom: 15px;
                min-width: auto;
                max-width: none;
            }
            .message-nickname { font-size: 1rem; }
            .message-content { font-size: 1rem; line-height: 1.7; }

            #load-more-btn { padding: 10px 25px; font-size: 1rem; }
        }
    </style>
</head>
<body>
    <!-- 背景图片容器 -->
    <div class="bg-container" id="bg-container">
        <!-- 背景图片通过JS动态添加 -->
    </div>
    <!-- 背景遮罩 -->
    <div class="bg-overlay"></div>

    <!-- 设备时钟对比区域 -->
    <div class="clock-diff" id="clockDiff">
        <div id="clockDiffText"></div>
    </div>
    

    
    <div class="container">
        <header>
            <h1>2026新年倒计时</h1>
            <p>迎接公历新年与农历春节，共赴美好新岁</p>
        </header>

        <!-- 优化后的倒计时区域 -->
        <div class="countdowns">
            <!-- 公历新年倒计时（2026-01-01 00:00:00） -->
            <div class="countdown-card">
                <div class="countdown-title">距离2026公历新年</div>
                <div class="countdown-time" id="countdown-solar">
                    <div class="time-item" id="solar-d">00</div>
                    <span>天</span>
                    <div class="time-item" id="solar-h">00</div>
                    <span>时</span>
                    <div class="time-item" id="solar-m">00</div>
                    <span>分</span>
                    <div class="time-item" id="solar-s">00</div>
                    <span>秒</span>
                </div>
            </div>

            <!-- 农历春节倒计时（2026-01-29 00:00:00，正月初一） -->
            <div class="countdown-card">
                <div class="countdown-title">距离2026农历春节</div>
                <div class="countdown-time" id="countdown-lunar">
                    <div class="time-item" id="lunar-d">00</div>
                    <span>天</span>
                    <div class="time-item" id="lunar-h">00</div>
                    <span>时</span>
                    <div class="time-item" id="lunar-m">00</div>
                    <span>分</span>
                    <div class="time-item" id="lunar-s">00</div>
                    <span>秒</span>
                </div>
            </div>
        </div>

        <!-- 祝福按钮区域 -->
        <div class="bless-section">
            <button id="bless-btn">祝福新年快乐 🎉</button>
            <p id="bless-count">已有 <span id="count-number">0</span> 人送上祝福</p>
        </div>

        <!-- 测试倒计时按钮区域 -->
        <div class="test-section">
            <button id="test-countdown-btn">🎆 提前体验倒计时结束效果 🎆</button>
        </div>

        <!-- 留言板区域 -->
        <div class="message-section">
            <h2 class="message-title">新年留言板</h2>
            <div class="error-message" id="message-error"></div>
            
            <!-- 表单容器：发布留言和筛选表单并排显示 -->
            <div style="display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 25px;">
                <!-- 发布留言表单 -->
                <form class="message-form" id="message-form" style="margin-bottom: 0;">
                <h3 style="margin-bottom: 15px; color: #fff; font-size: 1.2rem; text-shadow: 0 1px 3px rgba(0,0,0,0.2);">发布留言</h3>
                    <div class="form-group">
                        <label for="nickname">昵称</label>
                        <input type="text" id="nickname" name="nickname" placeholder="请输入你的昵称（必填）" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label for="content">留言内容</label>
                        <textarea id="content" name="content" rows="4" placeholder="写下你的新年祝福或愿望（必填）" required maxlength="500"></textarea>
                    </div>
                    <button type="submit" id="submit-btn">提交留言</button>
                </form>

                <!-- 留言筛选表单 -->
                <div class="filter-section" style="margin: 0;
                    background: rgba(255, 255, 255, 0.15);
                    padding: 20px;
                    border-radius: 12px;
                    border: 1px solid rgba(255, 255, 255, 0.2);">
                    <h3 style="margin-bottom: 15px; color: #fff; font-size: 1.2rem; text-shadow: 0 1px 3px rgba(0,0,0,0.2);">筛选留言</h3>
                    <form class="filter-form" id="filter-form">
                        <div style="display: grid;
                            grid-template-columns: 1fr;
                            gap: 15px;
                            margin-bottom: 15px;">
                            <div class="form-group">
                                <label for="filter-nickname">昵称</label>
                                <input type="text" id="filter-nickname" name="nickname" placeholder="按昵称搜索">
                            </div>
                            <div class="form-group">
                                <label for="filter-content">内容</label>
                                <input type="text" id="filter-content" name="content" placeholder="按内容搜索">
                            </div>
                            <div class="form-group">
                                <label for="filter-date">日期</label>
                                <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                                    <input type="date" id="filter-date-start" name="date_start" placeholder="开始日期" style="flex: 1;">
                                    <span style="color: #fff;">至</span>
                                    <input type="date" id="filter-date-end" name="date_end" placeholder="结束日期" style="flex: 1;">
                                </div>
                            </div>
                        </div>
                        <div style="display: flex;
                            gap: 10px;
                            flex-wrap: wrap;">
                            <button type="submit" style="background: linear-gradient(90deg, #4ecdc4, #3dbbba);
                                color: #fff;
                                padding: 10px 25px;
                                border: none;
                                border-radius: 8px;
                                font-size: 1rem;
                                cursor: pointer;
                                transition: all 0.3s;
                                box-shadow: 0 3px 12px rgba(78,205,196,0.3);">
                                筛选
                            </button>
                            <button type="button" id="reset-filter" style="background: rgba(255, 255, 255, 0.2);
                                color: #fff;
                                padding: 10px 25px;
                                border: 1px solid rgba(255, 255, 255, 0.3);
                                border-radius: 8px;
                                font-size: 1rem;
                                cursor: pointer;
                                transition: all 0.3s;
                                box-shadow: 0 3px 12px rgba(0,0,0,0.1);">
                                重置
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- 响应式布局：在小屏幕设备上自动调整为垂直布局 -->
            <style>
                @media (max-width: 992px) {
                    .message-section > div:first-of-type {
                        grid-template-columns: 1fr !important;
                    }
                }
            </style>

            <div class="message-list" id="message-list">
                <!-- 留言将通过JS动态加载 -->
            </div>

            <div id="load-more" style="display: none;">
                <button id="load-more-btn">查看更多留言</button>
            </div>
        </div>
    </div>

    <!-- 烟花特效容器 -->
    <canvas id="fireworks"></canvas>

    <script>
        // 1. 背景图片固定显示功能（优先使用本地1.jpg文件）
        (function() {
            const bgContainer = document.getElementById('bg-container');
            // 背景图片列表（优先使用本地1.jpg，然后使用网络图片）
            const bgImages = [
                '1.jpg', // 本地1.jpg文件，优先显示
                'https://images.unsplash.com/photo-1513486852956-0020c96b74b3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80',
                'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80'
            ];

            // 初始化背景图片（固定显示，不轮换）
            function initBackground() {
                // 创建背景元素
                const imgElement = document.createElement('div');
                imgElement.className = 'bg-image active';
                imgElement.style.backgroundImage = 'url(1.jpg)';
                imgElement.style.zIndex = '2'; // 确保有最高优先级
                imgElement.style.backgroundAttachment = 'fixed'; // 固定背景
                
                // 1.jpg加载失败时的处理
                imgElement.onerror = function() {
                    console.log('本地1.jpg加载失败，尝试使用网络图片');
                    // 加载失败时使用第一张网络图片
                    this.style.backgroundImage = `url(${bgImages[1]})`;
                };
                
                bgContainer.appendChild(imgElement);
            }

            // 初始化背景（只显示一次，不轮换）
            initBackground();
        })();

        // 2. 倒计时功能（含结束后庆祝效果）
        let solarPopupShown = false;
        let lunarPopupShown = false;

        // 添加烟花重复发射的状态变量
        let fireworksInterval = null;

        function updateCountdown() {
            // 目标时间：公历新年（2026-01-01 00:00:00）和春节（2026-01-29 00:00:00）
            const solarNewYear = new Date('2026-01-01T00:00:00').getTime();
            const lunarNewYear = new Date('2026-01-29T00:00:00').getTime();
            const now = new Date().getTime();

            // 计算时间差（处理已过期情况）
            const solarDiff = solarNewYear - now > 0 ? solarNewYear - now : 0;
            const lunarDiff = lunarNewYear - now > 0 ? lunarNewYear - now : 0;

            // 转换为天时分秒（判断是否已过期）
            const formatTime = (diff) => {
                if (diff === 0) {
                    return ['新年', '已到', '🎉', '🎉']; // 过期后显示的文案
                }
                const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);
                return [d, h, m, s].map(num => num.toString().padStart(2, '0'));
            };

            // 更新公历新年倒计时
            const [sD, sH, sM, sS] = formatTime(solarDiff);
            document.getElementById('solar-d').textContent = sD;
            document.getElementById('solar-h').textContent = sH;
            document.getElementById('solar-m').textContent = sM;
            document.getElementById('solar-s').textContent = sS;

            // 更新农历春节倒计时
            const [lD, lH, lM, lS] = formatTime(lunarDiff);
            document.getElementById('lunar-d').textContent = lD;
            document.getElementById('lunar-h').textContent = lH;
            document.getElementById('lunar-m').textContent = lM;
            document.getElementById('lunar-s').textContent = lS;

            // 公历新年倒计时结束效果（烟花+高亮）
            if (solarDiff === 0 && !solarPopupShown) {
                solarPopupShown = true;
                document.querySelector('#countdown-solar').parentElement.style.background = 'rgba(255, 210, 102, 0.3)';
                // 显示庆祝文字
                showCelebrationText('🎉 2026公历新年已到！祝你新年快乐，万事顺意！🎉');
                // 延迟800毫秒后显示烟花，确保庆祝文字先渐显
                setTimeout(() => {
                    fireworks.launch(15);
                    // 启动烟花持续发射，每3秒发射一次
                    if (!fireworksInterval) {
                        fireworksInterval = setInterval(() => {
                            fireworks.launch(10);
                        }, 3000);
                    }
                }, 800);
            }

            // 农历春节倒计时结束效果（烟花+高亮）
            if (lunarDiff === 0 && !lunarPopupShown) {
                lunarPopupShown = true;
                document.querySelector('#countdown-lunar').parentElement.style.background = 'rgba(255, 154, 139, 0.3)';
                // 显示庆祝文字
                showCelebrationText('🧧 2026农历春节快乐！愿你阖家幸福，龙年大吉！🧧');
                // 延迟800毫秒后显示烟花，确保庆祝文字先渐显
                setTimeout(() => {
                    fireworks.launch(15);
                    // 启动烟花持续发射，每3秒发射一次
                    if (!fireworksInterval) {
                        fireworksInterval = setInterval(() => {
                            fireworks.launch(10);
                        }, 3000);
                    }
                }, 800);
            }
        }

        // 2. 设备时钟与标准时间对比功能（从国内NTP服务器获取时间）
        (function() {
            const clockDiffElement = document.getElementById('clockDiff');
            const clockDiffText = document.getElementById('clockDiffText');
            
            // 显示加载状态
            clockDiffText.innerHTML = `<div style="text-align: center; padding: 10px;">正在获取标准时间...</div>`;
            
            // 格式化时间函数
            function formatDateTime(timestamp) {
                const date = new Date(timestamp);
                return date.toLocaleString('zh-CN', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    timeZoneName: 'short'
                });
            }
            
            // 从国内时间服务器获取标准时间
            function getStandardTime() {
                // 使用中国科学院国家授时中心的时间API
                return fetch('https://api.m.taobao.com/rest/api3.do?api=mtop.common.getTimestamp')
                    .then(response => response.json())
                    .then(data => {
                        if (data.ret && data.ret[0] === 'SUCCESS::接口调用成功' && data.data) {
                            return parseInt(data.data.t);
                        } else {
                            // 备用：使用京东时间API
                            return fetch('https://a.jd.com//ajax/queryServerData.html')
                                .then(response => response.text())
                                .then(text => {
                                    const match = text.match(/__jdsdt="(\d+)"/);
                                    if (match && match[1]) {
                                        return parseInt(match[1]);
                                    } else {
                                        throw new Error('无法获取标准时间');
                                    }
                                });
                        }
                    })
                    .catch(error => {
                        console.error('获取标准时间失败：', error);
                        // 如果所有API都失败，使用当前服务器时间作为备选
                        return <?php echo time() * 1000; ?>;
                    });
            }
            
            // 获取并显示时钟差异
            getStandardTime().then(standardTimestamp => {
                const clientTime = Date.now();
                const timeDiff = Math.abs(standardTimestamp - clientTime) / 1000; // 转换为秒
                const standardTimeSec = Math.floor(standardTimestamp / 1000);
                const clientTimeSec = Math.floor(clientTime / 1000);
                
                // 显示时钟差异和专业数据
                if (timeDiff < 5) {
                    clockDiffText.innerHTML = `
                        <div class="accurate">您的设备时钟非常准确！</div>
                        <div style="margin-top: 8px; font-size: 0.9rem; opacity: 0.9;">
                            <div>标准时间: ${formatDateTime(standardTimestamp)}</div>
                            <div>设备时间: ${formatDateTime(clientTime)}</div>
                            <div>时间差: ${timeDiff.toFixed(1)} 秒</div>
                            <div>时区: ${Intl.DateTimeFormat().resolvedOptions().timeZone}</div>
                        </div>
                        <div style="margin-top: 10px; font-size: 0.85rem; opacity: 0.8;">(点击可隐藏此提示)</div>
                        <div style="margin-top: 10px; font-size: 0.9rem; opacity: 0.9; color: #ffd700;">
                            如果您不喜欢泡泡留言特效，您可以点击"新年留言版"那几个字来隐藏
                        </div>
                    `;
                } else {
                    clockDiffText.innerHTML = `
                        <div class="inaccurate">当前您的设备时钟和标准时间差了${timeDiff.toFixed(1)}秒</div>
                        <div style="margin-top: 8px; font-size: 0.9rem; opacity: 0.9;">
                            <div>标准时间: ${formatDateTime(standardTimestamp)}</div>
                            <div>设备时间: ${formatDateTime(clientTime)}</div>
                            <div>建议校准您的设备时钟</div>
                        </div>
                        <div style="margin-top: 10px; font-size: 0.85rem; opacity: 0.8;">(点击可隐藏此提示)</div>
                        <div style="margin-top: 10px; font-size: 0.9rem; opacity: 0.9; color: #ffd700;">
                            如果您不喜欢泡泡留言特效，您可以点击"新年留言版"那几个字来隐藏
                        </div>
                    `;
                }
            });
            
            // 点击隐藏时钟对比
            clockDiffElement.addEventListener('click', function() {
                this.classList.add('hidden');
            });
        })();

        // 初始化倒计时并每秒更新
        updateCountdown();
        setInterval(updateCountdown, 1000);

        // 3. 祝福按钮功能（兼容BIGINT计数）
        const blessBtn = document.getElementById('bless-btn');
        const countNumber = document.getElementById('count-number');

        // 加载初始祝福数
        console.log('开始获取初始祝福数...');
        fetch('api/bless.php')
            .then(res => {
                console.log('初始祝福数响应状态:', res.status);
                console.log('初始祝福数响应状态文本:', res.statusText);
                console.log('初始祝福数响应头部:', Object.fromEntries(res.headers));
                return res.text().then(text => {
                    console.log('初始祝福数原始响应文本:', text);
                    try {
                        return JSON.parse(text);
                    } catch (err) {
                        console.error('初始祝福数JSON解析失败:', err);
                        throw new Error(`JSON解析失败: ${err.message}，原始文本: ${text}`);
                    }
                });
            })
            .then(data => {
                console.log('祝福初始数据:', data);
                if (data.success) countNumber.textContent = data.count;
            })
            .catch(err => {
                console.error('获取初始祝福数失败：', err);
                alert(`获取初始祝福数失败: ${err.message}，请查看控制台获取详细信息`);
            });

        blessBtn.addEventListener('click', () => {
            blessBtn.disabled = true;
            blessBtn.textContent = '发送中...';

            // 添加更详细的错误信息
            console.log('开始发送祝福请求...');
            console.log('请求URL:', 'api/bless.php');
            
            fetch('api/bless.php', { method: 'POST' })
                .then(res => {
                    console.log('祝福请求响应状态:', res.status);
                    console.log('祝福请求响应状态文本:', res.statusText);
                    console.log('祝福请求响应头部:', Object.fromEntries(res.headers));
                    return res.text().then(text => {
                        console.log('祝福请求原始响应文本:', text);
                        try {
                            return JSON.parse(text);
                        } catch (err) {
                            console.error('祝福请求JSON解析失败:', err);
                            throw new Error(`JSON解析失败: ${err.message}，原始文本: ${text}`);
                        }
                    });
                })
                .then(data => {
                    console.log('响应数据:', data);
                    if (data.success) {
                        countNumber.textContent = data.count;
                        alert('祝福发送成功！🎉');
                    } else {
                        // 显示后端返回的具体错误信息，包括IP限制提示
                        alert(data.message);
                    }
                })
                .catch(err => {
                    console.error('祝福请求失败：', err);
                    alert(`网络错误: ${err.message}，请检查浏览器控制台获取详细信息`);
                })
                .finally(() => {
                    blessBtn.disabled = false;
                    blessBtn.textContent = '祝福新年快乐 🎉';
                });
        });

        // 祝福数实时更新（轮询）- 优化版本（高性能）
        let isUpdatingBlessCount = false;
        let blessCountRetryDelay = 1000; // 初始重试延迟1秒
        let lastBlessUpdateTime = Date.now(); // 上次更新时间
        
        function updateBlessCount() {
            // 避免重复请求
            if (isUpdatingBlessCount) return;
            
            // 限制更新频率，至少间隔10秒
            const now = Date.now();
            if (now - lastBlessUpdateTime < 10000) {
                return;
            }
            
            isUpdatingBlessCount = true;
            lastBlessUpdateTime = now;
            
            fetch('api/bless.php')
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP错误，状态码: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success && data.count !== countNumber.textContent) {
                        countNumber.textContent = data.count;
                    }
                    // 请求成功，重置重试延迟
                    blessCountRetryDelay = 1000;
                })
                .catch(err => {
                    // 请求失败，增加重试延迟，最多30秒
                    blessCountRetryDelay = Math.min(blessCountRetryDelay * 2, 30000);
                    console.warn(`获取祝福数失败，${blessCountRetryDelay / 1000}秒后重试：`, err.message);
                })
                .finally(() => {
                    isUpdatingBlessCount = false;
                });
        }

        // 每10秒更新一次祝福数
        setInterval(updateBlessCount, 10000);

        // 4. 留言板功能（强化URL检测+IP频率限制前端提示）
        const messageForm = document.getElementById('message-form');
        const messageList = document.getElementById('message-list');
        const messageError = document.getElementById('message-error');
        const loadMoreBtn = document.getElementById('load-more-btn');
        const loadMoreDiv = document.getElementById('load-more');

        let showAll = true; // 默认显示所有留言

        // 常见域名后缀列表（可扩展）
        const commonDomainSuffixes = ['com', 'cn', 'net', 'org', 'gov', 'edu', 'io', 'cc', 'top', 'xyz', 'vip', 'club', 'info', 'biz', 'tv', 'me'];

        // 分页和筛选状态
        let currentPage = 1;
        let totalPages = 1;
        let totalMessages = 0;

        // 存储当前留言列表，用于比较新留言
        let currentMessages = [];
        
        // 筛选状态
        let currentFilters = {
            nickname: '',
            content: '',
            date: ''
        };

        // 加载留言（支持分页和筛选）- 优化版本
        function loadMessages(filters = null, resetPage = false) {
            // 更新筛选条件
            if (filters) {
                currentFilters = filters;
            }
            
            // 重置页码
            if (resetPage) {
                currentPage = 1;
            }
            
            // 显示加载状态
            messageList.innerHTML = '<p style="text-align: center; padding: 25px; color: #666; font-size: 1.1rem;">加载中...</p>';
            
            // 构建查询参数
            let queryParams = new URLSearchParams();
            queryParams.append('page', currentPage);
            
            // 添加筛选参数
            if (currentFilters.nickname) {
                queryParams.append('nickname', currentFilters.nickname);
            }
            if (currentFilters.content) {
                queryParams.append('content', currentFilters.content);
            }
            if (currentFilters.date) {
                queryParams.append('date', currentFilters.date);
            }
            
            fetch(`api/message.php?${queryParams.toString()}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const messages = data.messages;
                        messageList.innerHTML = '';

                        if (messages.length === 0) {
                            messageList.innerHTML = '<p style="text-align: center; padding: 25px; color: #666; font-size: 1.1rem;">暂无匹配的留言</p>';
                            currentMessages = [];
                            return;
                        }

                        // 更新分页状态
                        currentPage = data.page;
                        totalPages = data.total_pages;
                        totalMessages = data.total;

                        // 更新当前留言列表
                        currentMessages = messages;

                        // 使用文档片段来提高DOM操作性能
                        const fragment = document.createDocumentFragment();

                        // 渲染留言
                        messages.forEach(msg => {
                            const msgItem = document.createElement('div');
                            msgItem.className = 'message-item';
                            
                            // 根据留言特征判断是否需要独占一行
                            const isLongMessage = msg.content.length > 200;
                            const containsHtml = /<[^>]+>/.test(msg.content);
                            const isAdmin = msg.nickname === '站长';
                            
                            if (isLongMessage || containsHtml || isAdmin) {
                                msgItem.classList.add('full-width');
                            }
                            
                            msgItem.innerHTML = `
                                <div class="message-nickname">
                                    <span>${msg.nickname}</span>
                                    <span class="message-time">${formatTime(msg.created_at)}</span>
                                </div>
                                <div class="message-content">${msg.content}</div>
                            `;
                            
                            // 添加到文档片段
                            fragment.appendChild(msgItem);
                        });

                        // 一次性将所有留言添加到DOM中
                        messageList.appendChild(fragment);

                        // 显示分页控件
                        updatePagination();
                    } else {
                        messageError.textContent = data.message;
                        messageError.style.display = 'block';
                    }
                })
                .catch(err => {
                    console.error('加载留言失败：', err);
                    messageList.innerHTML = '<p style="text-align: center; padding: 25px; color: #666; font-size: 1.1rem;">加载留言失败，请稍后重试</p>';
                });
        }

        // 实时更新留言列表 - 优化版本（高性能）
        let isUpdatingMessages = false;
        let messagesRetryDelay = 1000; // 初始重试延迟1秒
        let lastUpdateTime = Date.now(); // 上次更新时间
        
        function updateMessages() {
            // 避免重复请求
            if (isUpdatingMessages) return;
            
            // 限制更新频率，至少间隔30秒
            const now = Date.now();
            if (now - lastUpdateTime < 30000) {
                return;
            }
            
            isUpdatingMessages = true;
            lastUpdateTime = now;
            
            fetch('api/message.php?page=1')
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP错误，状态码: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        const newMessages = data.messages;
                        
                        // 如果当前没有留言，直接加载
                        if (currentMessages.length === 0) {
                            loadMessages();
                            return;
                        }
                        
                        // 比较新留言与当前留言，找出新增的留言
                        const currentMessageIds = currentMessages.map(msg => msg.id);
                        const addedMessages = newMessages.filter(msg => !currentMessageIds.includes(msg.id));
                        
                        // 如果有新增留言，按照日期顺序插入到正确位置
                        if (addedMessages.length > 0) {
                            // 对新增留言按照日期倒序排序
                            addedMessages.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                            
                            // 将新增留言添加到当前留言列表
                            currentMessages = [...addedMessages, ...currentMessages];
                            
                            // 对当前留言列表进行去重，避免重复
                            const uniqueMessages = [...new Map(currentMessages.map(msg => [msg.id, msg])).values()];
                            currentMessages = uniqueMessages;
                            
                            // 对当前留言列表按照日期倒序排序
                            currentMessages.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                            
                            // 只渲染新增的留言，而不是整个列表
                            // 首先获取当前DOM中的所有留言元素
                            const currentMessageElements = messageList.querySelectorAll('.message-item');
                            const currentDomIds = Array.from(currentMessageElements).map(el => {
                                // 从元素内容中提取留言ID
                                // 注意：这里假设留言ID在DOM中没有直接存储，需要从服务器数据中映射
                                // 为了简化，我们直接重新渲染前几条留言
                                return null;
                            });
                            
                            // 简单优化：只重新渲染前20条留言，因为后面的留言用户可能看不到
                            const messagesToRender = currentMessages.slice(0, 20);
                            
                            // 重新渲染前20条留言
                            messageList.innerHTML = '';
                            messagesToRender.forEach(msg => {
                                const msgItem = document.createElement('div');
                                msgItem.className = 'message-item';
                                
                                // 根据留言特征判断是否需要独占一行
                                const isLongMessage = msg.content.length > 200;
                                const containsHtml = /<[^>]+>/.test(msg.content);
                                const isAdmin = msg.nickname === '站长';
                                
                                if (isLongMessage || containsHtml || isAdmin) {
                                    msgItem.classList.add('full-width');
                                }
                                
                                msgItem.innerHTML = `
                                    <div class="message-nickname">
                                        <span>${msg.nickname}</span>
                                        <span class="message-time">${formatTime(msg.created_at)}</span>
                                    </div>
                                    <div class="message-content">${msg.content}</div>
                                `;
                                
                                messageList.appendChild(msgItem);
                            });
                        }
                    }
                    // 请求成功，重置重试延迟
                    messagesRetryDelay = 1000;
                })
                .catch(err => {
                    // 请求失败，增加重试延迟，最多30秒
                    messagesRetryDelay = Math.min(messagesRetryDelay * 2, 30000);
                    console.warn(`获取留言失败，${messagesRetryDelay / 1000}秒后重试：`, err.message);
                })
                .finally(() => {
                    isUpdatingMessages = false;
                });
        }

        // 每30秒更新一次留言列表
        setInterval(updateMessages, 30000);

        // 更新分页控件
        function updatePagination() {
            // 创建分页HTML
            let paginationHtml = `
                <div class="pagination" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 20px;">
                    <button id="prev-page" ${currentPage === 1 ? 'disabled' : ''} style="padding: 8px 16px; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 8px; color: white; cursor: pointer; transition: all 0.3s ease;">上一页</button>
                    <span style="color: white; font-size: 1rem;">第 ${currentPage} / ${totalPages} 页，共 ${totalMessages} 条留言</span>
                    <button id="next-page" ${currentPage === totalPages ? 'disabled' : ''} style="padding: 8px 16px; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 8px; color: white; cursor: pointer; transition: all 0.3s ease;">下一页</button>
                </div>
            `;
            
            // 更新分页控件
            loadMoreDiv.innerHTML = paginationHtml;
            loadMoreDiv.style.display = 'block';
            
            // 添加事件监听
            document.getElementById('prev-page').addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    loadMessages();
                }
            });
            
            document.getElementById('next-page').addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadMessages();
                }
            });
        }

        // 修复：准确判断今天/昨天/具体日期（避免跨凌晨误判）
        function formatTime(timeStr) {
            const date = new Date(timeStr);
            const now = new Date();

            // 防止无效时间字符串导致的异常
            if (isNaN(date.getTime())) {
                return timeStr; // 若时间格式错误，直接显示原始字符串
            }

            // 提取「留言时间」的年、月、日（去掉时分秒干扰）
            const msgYear = date.getFullYear();
            const msgMonth = date.getMonth() + 1; // 月份从0开始，需+1
            const msgDate = date.getDate();

            // 提取「当前时间」的年、月、日
            const nowYear = now.getFullYear();
            const nowMonth = now.getMonth() + 1;
            const nowDate = now.getDate();

            // 格式化时分（补0）
            const hour = date.getHours().toString().padStart(2, '0');
            const minute = date.getMinutes().toString().padStart(2, '0');

            // 1. 年、月、日完全相同 → 今天
            if (msgYear === nowYear && msgMonth === nowMonth && msgDate === nowDate) {
                return `今天 ${hour}:${minute}`;
            }

            // 2. 留言日期是当前日期的前一天 → 昨天
            // （通过计算“当前日期时间戳 - 留言日期时间戳”是否在1~2天内判断）
            const oneDayMs = 24 * 60 * 60 * 1000; // 一天的毫秒数
            const timeDiff = now.getTime() - date.getTime();
            if (timeDiff > oneDayMs && timeDiff <= 2 * oneDayMs) {
                return `昨天 ${hour}:${minute}`;
            }

            // 3. 其他情况 → 显示年月日（补0）
            return `${msgYear}-${msgMonth.toString().padStart(2, '0')}-${msgDate.toString().padStart(2, '0')}`;
        }

        // 漂浮泡泡功能
        (function() {
            // 创建泡泡容器
            const bubbleContainer = document.createElement('div');
            bubbleContainer.className = 'bubble-container';
            document.body.appendChild(bubbleContainer);

            // 从留言中获取祝福（只显示用户的祝福）
            let messageWishes = [];
            
            // 泡泡状态：true为显示，false为隐藏
            let bubbleEnabled = true;
            
            // 泡泡动画间隔ID
            let bubbleInterval = null;

            // 获取留言作为祝福
            function fetchMessageWishes() {
                fetch('api/message.php?showAll=1')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && Array.isArray(data.messages)) {
                            // 提取留言内容作为祝福
                            messageWishes = data.messages.map(msg => msg.content);
                        }
                    })
                    .catch(err => {
                        console.error('获取留言祝福失败：', err);
                    });
            }

            // 创建随机泡泡
            function createBubble() {
                if (!bubbleEnabled) return;
                
                const bubble = document.createElement('div');
                bubble.className = 'bubble';

                // 只使用用户的留言作为祝福
                let wishContent;
                if (messageWishes.length === 0) {
                    // 如果没有留言，显示默认祝福
                    const defaultWishes = ['新年快乐！🎉', '万事如意！✨', '恭喜发财！💰'];
                    wishContent = defaultWishes[Math.floor(Math.random() * defaultWishes.length)];
                } else {
                    // 随机选择用户留言作为祝福内容
                    wishContent = messageWishes[Math.floor(Math.random() * messageWishes.length)];
                }

                // 检查是否包含HTML标签，如果包含则跳过
                const hasHtmlTags = /<[^>]+>/.test(wishContent);
                if (hasHtmlTags) {
                    // 包含HTML标签，不显示该泡泡
                    return;
                }
                
                // 设置泡泡内容，限制长度
                const maxLength = 50;
                if (wishContent.length > maxLength) {
                    wishContent = wishContent.substring(0, maxLength) + '...';
                }
                bubble.textContent = wishContent;

                // 随机设置泡泡位置
                const randomLeft = Math.random() * (window.innerWidth - 250);
                
                // 初始位置：屏幕底部，设置transform代替top，确保动画从底部开始
                bubble.style.left = `${randomLeft}px`;
                bubble.style.transform = 'translateY(100vh) scale(0.9)';
                bubble.style.opacity = '0';

                // 添加到容器
                bubbleContainer.appendChild(bubble);

                // 触发重排，确保动画能正常开始
                bubble.offsetHeight;
                
                // 设置动画，使用setTimeout确保样式已应用
                setTimeout(() => {
                    bubble.style.transition = 'all 0s';
                    bubble.style.animation = 'float 8s ease-in-out forwards';
                    bubble.style.opacity = '0.85';
                }, 10);

                // 动画结束后移除泡泡
                setTimeout(() => {
                    if (bubble.parentNode) {
                        bubble.parentNode.removeChild(bubble);
                    }
                }, 8000);
            }

            // 定期创建泡泡
            function startBubbleAnimation() {
                // 初始创建更多泡泡
                for (let i = 0; i < 5; i++) {
                    setTimeout(createBubble, i * 800);
                }

                // 增加创建频率
                bubbleInterval = setInterval(createBubble, 1500);
            }

            // 停止泡泡动画
            function stopBubbleAnimation() {
                if (bubbleInterval) {
                    clearInterval(bubbleInterval);
                    bubbleInterval = null;
                }
                // 移除所有现有泡泡
                while (bubbleContainer.firstChild) {
                    bubbleContainer.removeChild(bubbleContainer.firstChild);
                }
            }

            // 切换泡泡状态
            function toggleBubbleEffect() {
                bubbleEnabled = !bubbleEnabled;
                
                if (bubbleEnabled) {
                    // 开启泡泡
                    startBubbleAnimation();
                } else {
                    // 关闭泡泡
                    stopBubbleAnimation();
                }
            }

            // 绑定新年留言版标题点击事件
            const messageTitle = document.querySelector('.message-title');
            if (messageTitle) {
                messageTitle.addEventListener('click', toggleBubbleEffect);
            }

            // 获取留言祝福
            fetchMessageWishes();

            // 开始泡泡动画
            startBubbleAnimation();
        })();

        // 初始化加载留言（默认显示所有）
        loadMessages();

        // 筛选表单事件监听
        const filterForm = document.getElementById('filter-form');
        const resetFilterBtn = document.getElementById('reset-filter');
        
        // 筛选表单提交事件
        filterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // 获取筛选条件
            const nickname = document.getElementById('filter-nickname').value.trim();
            const content = document.getElementById('filter-content').value.trim();
            const dateStart = document.getElementById('filter-date-start').value.trim();
            const dateEnd = document.getElementById('filter-date-end').value.trim();
            
            // 构建日期筛选字符串
            let date = '';
            if (dateStart && dateEnd) {
                date = `${dateStart}至${dateEnd}`;
            } else if (dateStart) {
                date = dateStart;
            } else if (dateEnd) {
                date = dateEnd;
            }
            
            // 构建筛选对象
            const filters = {
                nickname: nickname,
                content: content,
                date: date
            };
            
            // 加载筛选结果
            loadMessages(filters, true);
        });
        
        // 重置筛选按钮事件
        resetFilterBtn.addEventListener('click', () => {
            // 清空筛选表单
            filterForm.reset();
            
            // 清空日期选择器
            document.getElementById('filter-date-start').value = '';
            document.getElementById('filter-date-end').value = '';
            
            // 重置筛选条件并重新加载留言
            loadMessages({ nickname: '', content: '', date: '' }, true);
        });

        // 提交留言（强化URL检测：含无协议头、拆分链接、常见后缀）
        messageForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const nickname = document.getElementById('nickname').value.trim();
            let content = document.getElementById('content').value.trim();

            // 前端简单验证
            if (!nickname || !content) {
                messageError.textContent = '昵称和留言内容不能为空！';
                messageError.style.display = 'block';
                return;
            }

            // 1. 去除所有空格（防止拆分链接：如 baidu . com → baidu.com）
            const cleanContent = content.replace(/\s+/g, '');

            // 2. 增强版URL检测正则（拦截所有链接形式）
            const urlReg = /(https?:\/\/|www\.|([a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?)|(\d+\.\d+\.\d+\.\d+(:\d+)?))(\/[^\s]*)?/i;
            if (urlReg.test(content) || urlReg.test(cleanContent)) {
                messageError.textContent = '留言内容不能包含任何形式的链接（如网站地址、IP地址等）！';
                messageError.style.display = 'block';
                return;
            }

            // 3. 检测常见域名后缀（防止漏网之鱼）
            const suffixReg = new RegExp(`\\.(${commonDomainSuffixes.join('|')})(\\/|$)`, 'i');
            if (suffixReg.test(content) || suffixReg.test(cleanContent)) {
                messageError.textContent = '留言内容不能包含任何形式的链接（如网站地址、IP地址等）！';
                messageError.style.display = 'block';
                return;
            }

            messageError.style.display = 'none';
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = '提交中...';

            // 发送留言请求
            console.log('开始发送留言请求...');
            console.log('请求URL:', 'api/message.php');
            console.log('请求数据:', { nickname, content: content.substring(0, 20) + '...' });
            
            fetch('api/message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `nickname=${encodeURIComponent(nickname)}&content=${encodeURIComponent(content)}`
            })
                .then(res => {
                    console.log('留言响应状态:', res.status);
                    console.log('留言响应状态文本:', res.statusText);
                    if (!res.ok) {
                        throw new Error(`网络请求失败，状态码: ${res.status}`);
                    }
                    return res.json().catch(err => {
                        throw new Error(`JSON解析失败: ${err.message}`);
                    });
                })
                .then(data => {
                    console.log('留言响应数据:', data);
                    if (data.success) {
                        alert('留言提交成功！🎉');
                        messageForm.reset();
                        loadMessages(); // 刷新留言列表（默认显示所有）
                        messageError.style.display = 'none';
                    } else {
                        // 显示频率限制等后端错误提示
                        messageError.textContent = data.message;
                        messageError.style.display = 'block';
                    }
                })
                .catch(err => {
                    console.error('留言提交失败：', err);
                    messageError.textContent = `网络错误: ${err.message}，请检查浏览器控制台获取详细信息`;
                    messageError.style.display = 'block';
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = '提交留言';
                });
        });



        // 4. 烟花特效实现（优化版）
        // 整合文章中的更好烟花效果
        class Fireworks {
            constructor() {
                this.canvas = document.getElementById('fireworks');
                this.ctx = this.canvas.getContext('2d');
                this.fireworks = [];
                this.particles = [];
                this.mouse = {
                    x: 0,
                    y: 0,
                    click: false
                };
                
                // 增加多种粒子形状和效果
                this.particleShapes = ['circle', 'circle', 'circle', 'circle'];
                
                this.setup();
                this.loop();
                this.bindEvents();
            }
            
            setup() {
                this.canvas.width = window.innerWidth;
                this.canvas.height = window.innerHeight;
            }
            
            bindEvents() {
                window.addEventListener('resize', () => this.setup());
                
                window.addEventListener('mousemove', (e) => {
                    this.mouse.x = e.clientX;
                    this.mouse.y = e.clientY;
                });
            }
            
            // 生成更丰富的颜色
            generateColor(baseHue, variation) {
                const hue = Math.max(0, Math.min(360, baseHue + (Math.random() * variation - variation / 2)));
                return hue;
            }
            
            // 创建更漂亮的烟花
            createFirework(x, y) {
                const baseHue = Math.random() * 360;
                const firework = {
                    x: this.canvas.width / 2,
                    y: this.canvas.height,
                    startX: this.canvas.width / 2,
                    startY: this.canvas.height,
                    targetX: x,
                    targetY: y,
                    hue: baseHue,
                    brightness: Math.random() * 30 + 50,
                    speed: Math.random() * 3 + 1,
                    acceleration: 1.05,
                    targetRadius: 1,
                    trail: [],
                    trailLength: 10,
                    distanceToTarget: Math.sqrt(Math.pow(x - this.canvas.width / 2, 2) + Math.pow(y - this.canvas.height, 2)),
                    distanceTraveled: 0,
                    angle: Math.atan2(y - this.canvas.height, x - this.canvas.width / 2)
                };
                
                this.fireworks.push(firework);
            }
            
            // 创建更丰富的粒子效果 - 优化性能
            createParticles(x, y, hue) {
                // 动态调整粒子数量，在效果和性能间平衡
                const baseParticleCount = Math.floor(Math.random() * 20) + 40;
                let particleCount = baseParticleCount;
                
                // 根据设备性能动态调整粒子数量
                if (this.particles.length > 800) {
                    particleCount = Math.floor(baseParticleCount * 0.7);
                } else if (this.particles.length > 1000) {
                    particleCount = Math.floor(baseParticleCount * 0.5);
                }
                
                // 随机选择烟花类型
                const shellTypes = ['crysanthemum', 'palm', 'ring', 'crossette'];
                const shellType = shellTypes[Math.floor(Math.random() * shellTypes.length)];
                
                // 根据烟花类型调整参数
                let spreadSize = 300;
                let gravity = 0.2;
                
                switch(shellType) {
                    case 'crysanthemum':
                        spreadSize = 350;
                        gravity = 0.3;
                        break;
                    case 'palm':
                        spreadSize = 400;
                        gravity = 0.15;
                        break;
                    case 'ring':
                        spreadSize = 450;
                        gravity = 0.25;
                        break;
                    case 'crossette':
                        spreadSize = 380;
                        gravity = 0.4;
                        break;
                }
                
                for (let i = 0; i < particleCount; i++) {
                    // 计算角度，让粒子均匀分布
                    const angle = (Math.PI * 2 * i) / particleCount + Math.random() * 0.4 - 0.2;
                    
                    // 随机速度，让烟花炸得更开
                    const speed = Math.random() * 8 + 3;
                    
                    // 添加随机颜色变化
                    const colorVariation = Math.random() * 50 - 25;
                    const finalHue = hue + colorVariation;
                    
                    // 随机亮度
                    const brightness = Math.random() * 40 + 50;
                    
                    // 创建粒子
                    this.particles.push({
                        x: x,
                        y: y,
                        hue: finalHue,
                        brightness: brightness,
                        alpha: 1,
                        decay: Math.random() * 0.015 + 0.01,
                        size: Math.random() * 2 + 1,
                        velocity: {
                            x: Math.cos(angle) * speed * (spreadSize / 300),
                            y: Math.sin(angle) * speed * (spreadSize / 300)
                        },
                        gravity: gravity,
                        trail: [],
                        trailLength: 3, // 减少轨迹点数量
                        shape: 'circle',
                        // 减少闪烁效果粒子比例，优化性能
                        twinkle: Math.random() > 0.9,
                        twinkleSpeed: Math.random() * 0.03 + 0.02
                    });
                }
            }
            
            // 超级烟花效果
            createSuperFirework(x, y) {
                // 降低超级烟花的复杂度，防止性能问题
                this.createFirework(x, y);
            }
            
            update() {
                // 优化性能：降低粒子生成限制，防止过多粒子导致卡顿
                if (this.particles.length > 1000) {
                    return;
                }
                
                // 更新烟花
                for (let i = this.fireworks.length - 1; i >= 0; i--) {
                    const firework = this.fireworks[i];
                    
                    // 保存轨迹
                    firework.trail.push([firework.x, firework.y]);
                    if (firework.trail.length > firework.trailLength) {
                        firework.trail.shift();
                    }
                    
                    // 加速度效果
                    firework.speed *= firework.acceleration;
                    
                    // 调整最大速度，平衡性能和效果
                    if (firework.speed > 12) {
                        firework.speed = 12;
                    }
                    
                    // 计算速度分量
                    const vx = Math.cos(firework.angle) * firework.speed;
                    const vy = Math.sin(firework.angle) * firework.speed;
                    
                    // 优化性能：简化距离计算，使用平方比较代替开方
                    const dx = firework.targetX - firework.x;
                    const dy = firework.targetY - firework.y;
                    const distanceSquared = dx * dx + dy * dy;
                    
                    // 检查是否到达目标 - 性能优化：使用平方距离比较
                    if (distanceSquared <= 1600 || firework.y < firework.targetY) {
                        // 降低超级烟花生成概率，减少粒子生成
                        if (Math.random() > 0.85) {
                            this.createSuperFirework(firework.targetX, firework.targetY);
                        } else {
                            this.createParticles(firework.targetX, firework.targetY, firework.hue);
                        }
                        this.fireworks.splice(i, 1);
                    } else {
                        // 继续移动
                        firework.x += vx;
                        firework.y += vy;
                    }
                }
                
                // 更新粒子
                for (let i = this.particles.length - 1; i >= 0; i--) {
                    const particle = this.particles[i];
                    
                    // 保存轨迹 - 性能优化：只有高透明度粒子才保存轨迹
                    if (particle.alpha > 0.3) {
                        particle.trail.push([particle.x, particle.y]);
                        if (particle.trail.length > particle.trailLength) {
                            particle.trail.shift();
                        }
                    }
                    
                    // 应用重力
                    particle.velocity.y += particle.gravity;
                    
                    // 更新位置
                    particle.x += particle.velocity.x;
                    particle.y += particle.velocity.y;
                    
                    // 优化性能：增加减速系数，让粒子更快停止
                    particle.velocity.x *= 0.98;
                    particle.velocity.y *= 0.98;
                    
                    // 衰减透明度 - 优化性能：加快衰减，减少粒子生命周期
                    particle.alpha -= particle.decay * 1.5;
                    
                    // 处理闪烁效果 - 性能优化：降低计算频率
                    if (particle.twinkle && particle.alpha > 0.5) {
                        // 使用更简单的闪烁计算
                        const time = Date.now() * particle.twinkleSpeed;
                        particle.brightness = Math.max(50, Math.min(90, 70 + Math.sin(time) * 15));
                    }
                    
                    // 移除死亡粒子 - 更严格的条件，减少计算负担
                    if (particle.alpha <= 0.05 || particle.y > this.canvas.height + 50) {
                        this.particles.splice(i, 1);
                    }
                }
                
                // 优化性能：更严格的粒子数量限制
                if (this.particles.length > 1200) {
                    // 移除更多旧粒子
                    this.particles = this.particles.slice(-800);
                }
            }
            
            // 绘制烟花轨迹
            drawFirework(firework) {
                // 修复：添加空数组检查，防止数组越界错误
                if (firework.trail.length < 1) {
                    // 只绘制烟花头部
                    this.ctx.beginPath();
                    this.ctx.arc(firework.x, firework.y, firework.targetRadius * 2, 0, Math.PI * 2);
                    this.ctx.fillStyle = `hsla(${firework.hue}, 100%, ${firework.brightness}%, 1)`;
                    this.ctx.fill();
                    return;
                }
                
                // 绘制轨迹
                this.ctx.beginPath();
                this.ctx.moveTo(firework.trail[0][0], firework.trail[0][1]);
                for (let i = 1; i < firework.trail.length; i++) {
                    this.ctx.lineTo(firework.trail[i][0], firework.trail[i][1]);
                }
                
                this.ctx.strokeStyle = `hsla(${firework.hue}, 100%, ${firework.brightness}%, 0.8)`;
                this.ctx.lineWidth = firework.targetRadius * 2;
                this.ctx.stroke();
                
                // 绘制烟花头部
                this.ctx.beginPath();
                this.ctx.arc(firework.x, firework.y, firework.targetRadius * 2, 0, Math.PI * 2);
                this.ctx.fillStyle = `hsla(${firework.hue}, 100%, ${firework.brightness}%, 1)`;
                this.ctx.fill();
            }
            
            // 绘制粒子 - 优化性能，保持辉光效果
            drawParticle(particle) {
                // 确保粒子属性存在
                if (typeof particle.x !== 'number' || typeof particle.y !== 'number' || typeof particle.size !== 'number') {
                    return;
                }
                
                // 性能优化：根据粒子透明度动态调整绘制细节
                const detailLevel = particle.alpha > 0.3 ? 1 : 0.5;
                
                // 绘制轨迹 - 性能优化：减少绘制调用次数
                if (particle.trail && particle.trail.length > 1 && detailLevel > 0.5) {
                    try {
                        // 轨迹渐变效果 - 性能优化：使用单次路径绘制
                        this.ctx.strokeStyle = `hsla(${particle.hue}, 100%, ${particle.brightness}%, ${particle.alpha * 0.3})`;
                        this.ctx.lineWidth = particle.size * 0.6;
                        this.ctx.lineCap = 'round';
                        this.ctx.lineJoin = 'round';
                        
                        // 性能优化：降低阴影模糊半径
                        this.ctx.shadowBlur = 5;
                        this.ctx.shadowColor = `hsla(${particle.hue}, 100%, ${particle.brightness}%, ${particle.alpha * 0.3})`;
                        
                        // 单次路径绘制所有轨迹
                        this.ctx.beginPath();
                        this.ctx.moveTo(particle.trail[0][0], particle.trail[0][1]);
                        for (let i = 1; i < particle.trail.length; i++) {
                            if (particle.trail[i]) {
                                this.ctx.lineTo(particle.trail[i][0], particle.trail[i][1]);
                            }
                        }
                        this.ctx.stroke();
                    } catch (e) {
                        // 忽略绘制错误，继续执行
                    }
                }
                
                // 性能优化：根据粒子数量动态调整辉光强度
                let shadowBlur = 10;
                if (this.particles.length > 800) {
                    shadowBlur = 6;
                } else if (this.particles.length > 1000) {
                    shadowBlur = 4;
                }
                
                // 添加粒子辉光效果
                this.ctx.shadowBlur = shadowBlur;
                this.ctx.shadowColor = `hsla(${particle.hue}, 100%, ${particle.brightness}%, ${particle.alpha})`;
                
                // 绘制粒子主体
                this.ctx.beginPath();
                this.ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
                this.ctx.fillStyle = `hsla(${particle.hue}, 100%, ${particle.brightness}%, ${particle.alpha})`;
                this.ctx.fill();
                
                // 性能优化：减少高光绘制
                if (particle.alpha > 0.7) {
                    this.ctx.beginPath();
                    this.ctx.arc(particle.x - particle.size * 0.3, particle.y - particle.size * 0.3, particle.size * 0.3, 0, Math.PI * 2);
                    this.ctx.fillStyle = `hsla(0, 0%, 100%, ${particle.alpha * 0.4})`;
                    this.ctx.fill();
                }
                
                // 闪烁效果增强 - 性能优化：降低闪烁频率
                if (particle.twinkle && Math.random() > 0.9) {
                    this.ctx.beginPath();
                    this.ctx.arc(particle.x, particle.y, particle.size * 1.5, 0, Math.PI * 2);
                    this.ctx.fillStyle = `hsla(${particle.hue}, 100%, ${particle.brightness + 20}%, ${particle.alpha * 0.3})`;
                    this.ctx.fill();
                }
                
                // 重置阴影效果
                this.ctx.shadowBlur = 0;
                this.ctx.shadowColor = 'transparent';
            }
            
            draw() {
                // 直接清除画布，避免渐变黑屏
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                
                // 绘制烟花
                this.fireworks.forEach(firework => {
                    this.drawFirework(firework);
                });
                
                // 绘制粒子
                this.particles.forEach(particle => {
                    this.drawParticle(particle);
                });
            }
            
            loop() {
                this.update();
                this.draw();
                requestAnimationFrame(() => this.loop());
            }
            
            // 触发烟花效果
            launch(count = 10) {
                count = Math.min(count, 12);
                
                for (let i = 0; i < count; i++) {
                    setTimeout(() => {
                        const x = Math.random() * this.canvas.width * 0.8 + this.canvas.width * 0.1;
                        const y = Math.random() * this.canvas.height * 0.3 + this.canvas.height * 0.1;
                        this.createFirework(x, y);
                    }, i * 100);
                }
            }
        }
        
        // 初始化烟花
        const fireworks = new Fireworks();
        
        // 保存古诗词切换定时器
        let poemSwitchInterval = null;
        // 保存从API获取的古诗列表
        let springFestivalPoems = [];
        
        // 从API获取古诗列表
        async function fetchPoems() {
            try {
                const response = await fetch('api/poems.php');
                const data = await response.json();
                if (data.success) {
                    springFestivalPoems = data.poems;
                }
            } catch (error) {
                console.error('获取古诗列表失败:', error);
                // 失败时使用默认古诗列表
                springFestivalPoems = [
                    '爆竹声中一岁除，春风送暖入屠苏。',
                    '千门万户曈曈日，总把新桃换旧符。',
                    '故乡今夜思千里，霜鬓明朝又一年。'
                ];
            }
        }
        
        // 显示庆祝文字并定时切换古诗词
        async function showCelebrationText(text) {
            // 1. 自动隐藏时间差提示框，避免样式重叠
            const clockDiffElement = document.getElementById('clockDiff');
            if (clockDiffElement) {
                clockDiffElement.classList.add('hidden');
            }
            
            // 2. 为倒计时模块添加类，使其下移
            const countdownsContainer = document.querySelector('.countdowns');
            countdownsContainer.classList.add('countdown-ended');
            
            // 3. 页面变暗，突出烟花效果
            const overlay = document.createElement('div');
            overlay.id = 'fireworks-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 10;
                pointer-events: none;
                transition: background 0.5s ease;
            `;
            document.body.appendChild(overlay);
            
            // 4. 创建庆祝文字元素，使用新的CSS类
            const celebrationDiv = document.createElement('div');
            celebrationDiv.id = 'celebration-text';
            celebrationDiv.className = 'celebration-text';
            celebrationDiv.textContent = text;
            document.body.appendChild(celebrationDiv);
            
            // 5. 获取古诗列表
            await fetchPoems();
            
            // 6. 3秒后开始切换为随机古诗词，每5秒切换一次
            let lastIndex = -1;
            
            setTimeout(() => {
                // 切换诗词的函数
                const switchPoem = () => {
                    if (springFestivalPoems.length === 0) return;
                    
                    let randomIndex;
                    // 确保每次切换的诗词与上次不同
                    do {
                        randomIndex = Math.floor(Math.random() * springFestivalPoems.length);
                    } while (randomIndex === lastIndex);
                    
                    lastIndex = randomIndex;
                    celebrationDiv.textContent = springFestivalPoems[randomIndex];
                    
                    // 添加淡入淡出效果
                    celebrationDiv.style.opacity = '0';
                    setTimeout(() => {
                        celebrationDiv.style.opacity = '1';
                    }, 300);
                };
                
                // 第一次切换
                switchPoem();
                
                // 每5秒切换一次，保存定时器ID
                poemSwitchInterval = setInterval(switchPoem, 5000);
            }, 3000);
        }
        
        // 5. 测试按钮功能：提前到倒计时结束前十秒
        const testBtn = document.getElementById('test-countdown-btn');
        testBtn.addEventListener('click', () => {
            if (confirm('确定要提前体验倒计时结束效果吗？这将模拟倒计时结束前十秒的状态并开始倒计时。')) {
                // 保存原始的Date构造函数和当前时间
                const originalDate = Date;
                const startTime = new originalDate('2025-12-31T23:59:50').getTime();
                const now = originalDate.now();
                let offset = 0;
                let timerInterval = null;
                
                // 重写Date构造函数，返回基于固定时间点加上偏移量的时间
                Date = function(...args) {
                    if (args.length === 0) {
                        // 返回基于固定时间点加上偏移量的时间
                        return new originalDate(startTime + offset);
                    }
                    return new originalDate(...args);
                };
                
                // 复制静态方法
                Date.now = () => startTime + offset;
                Date.parse = originalDate.parse;
                Date.UTC = originalDate.UTC;
                
                // 重置标志位，以便再次触发庆祝效果
                solarPopupShown = false;
                lunarPopupShown = false;
                
                // 使用setInterval更新偏移量，让时间正常流逝
                timerInterval = setInterval(() => {
                    offset += 1000; // 每秒增加1秒
                    updateCountdown();
                }, 1000);
                
                // 35秒后恢复原始Date构造函数
                setTimeout(() => {
                    clearInterval(timerInterval);
                    // 清除烟花发射间隔
                    if (fireworksInterval) {
                        clearInterval(fireworksInterval);
                        fireworksInterval = null;
                    }
                    // 清除古诗词切换定时器
                    if (poemSwitchInterval) {
                        clearInterval(poemSwitchInterval);
                        poemSwitchInterval = null;
                    }
                    Date = originalDate;
                    
                    // 移除countdown-ended类，使倒计时模块归位
                    const countdownsContainer = document.querySelector('.countdowns');
                    countdownsContainer.classList.remove('countdown-ended');
                    
                    // 移除页面变暗效果
                    const overlay = document.getElementById('fireworks-overlay');
                    if (overlay) {
                        overlay.remove();
                    }
                    
                    // 移除庆祝文字元素
                    const celebrationText = document.getElementById('celebration-text');
                    if (celebrationText) {
                        celebrationText.remove();
                    }
                    
                    // 重置标志位
                    solarPopupShown = false;
                    lunarPopupShown = false;
                    
                    alert('倒计时结束效果体验完毕，已恢复正常时间。');
                }, 35000);
                
                // 立即更新一次倒计时
                updateCountdown();
            }
        });
    </script>
</body>
</html>