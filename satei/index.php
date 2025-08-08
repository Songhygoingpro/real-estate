<?php
/**
 * Property Assessment Form
 * 
 * Secure form handling with proper validation and sanitization
 */

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Start session securely
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
session_start();

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verify CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('CSRF token validation failed');
    }
    
    require_once __DIR__ . '/../src/classes/InquiryHandler.php';
    
    $handler = new InquiryHandler();
    $result = $handler->processInquiry($_POST, $_SESSION);
    
    if ($result['success']) {
        header('Location: ../satei/success.html');
        exit;
    } else {
        $errors = $result['errors'] ?? [];
        $errorMessage = $result['message'] ?? 'エラーが発生しました。';
    }
}

// Get session data for form population
$formData = [
    'property_type' => $_SESSION['物件の種別'] ?? '',
    'prefecture' => $_SESSION['prefecture'] ?? '',
    'city' => $_SESSION['city'] ?? '',
    'town' => $_SESSION['town'] ?? ''
];

// Sanitize form data for display
foreach ($formData as $key => $value) {
    $formData[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <title>査定フォーム - おうち買取.com</title>
    <link rel="icon" type="image/png" href="../assets/img/site-favicon.png">
    
    <!-- Content Security Policy -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com https://ajax.googleapis.com; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com;">
</head>

<body data-aos="custom-fadeUp">
    <header class="flex text-white justify-between items-center transition-colors p-4 md:px-10 w-full sticky top-0 z-10 bg-white shadow-md">
        <a href="../baikyaku/index.html" aria-label="ホームページに戻る">
            <img src="../assets/img/real-estate-logo.png" class="h-8 sm:h-9 w-auto" alt="おうち買取.com ロゴ">
        </a>
    </header>

    <main>
        <?php if (isset($errorMessage)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mx-4" role="alert">
            <strong class="font-bold">エラー:</strong>
            <span class="block sm:inline"><?php echo htmlspecialchars($errorMessage); ?></span>
        </div>
        <?php endif; ?>

        <section class="form-section flex justify-center items-center py-16">
            <div class="form-section__inner w-full max-w-[1040px] h-auto grid gap-6 px-4">
                <h1 class="text-3xl text-center font-bold">不動産査定フォーム</h1>
                
                <form class="grid gap-6" method="post" id="inquiriesForm" novalidate>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="grid border-0 md:border-[1px] border-gray-300 bg-white shadow-lg rounded-lg overflow-hidden">
                        
                        <!-- Property Type -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-red-500 p-1 text-white text-xs rounded">必須</span>
                                <label class="font-bold" for="property_type">物件種別</label>
                            </div>
                            <div class="flex justify-start items-center">
                                <span class="text-lg font-medium"><?php echo $formData['property_type']; ?></span>
                                <input type="hidden" name="property_type" value="<?php echo $formData['property_type']; ?>">
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-red-500 p-1 text-white text-xs rounded">必須</span>
                                <label class="font-bold">所在地</label>
                            </div>
                            <div class="grid gap-4">
                                <div class="text-lg font-medium">
                                    <?php echo $formData['prefecture'] . $formData['city'] . $formData['town']; ?>
                                </div>
                                <input type="hidden" name="prefecture" value="<?php echo $formData['prefecture']; ?>">
                                <input type="hidden" name="city" value="<?php echo $formData['city']; ?>">
                                <input type="hidden" name="town" value="<?php echo $formData['town']; ?>">
                                
                                <div>
                                    <label for="address_detail" class="block text-sm font-medium mb-1">丁目・番地・号 (入力例: 1-3-13)</label>
                                    <input type="text" name="address_detail" id="address_detail" 
                                           class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="1-3-13">
                                </div>
                                
                                <div>
                                    <label for="mansion_name" class="block text-sm font-medium mb-1">マンション名</label>
                                    <input type="text" name="mansion_name" id="mansion_name" 
                                           class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="マンション名を入力">
                                </div>
                                
                                <div>
                                    <label for="room_number" class="block text-sm font-medium mb-1">号室</label>
                                    <input type="text" name="room_number" id="room_number" 
                                           class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="101">
                                </div>
                                
                                <p class="text-red-600 text-sm">
                                    番地など住所情報にお間違えがないかご確認ください（不足していると、査定が実施できません）
                                </p>
                            </div>
                        </div>

                        <!-- Layout -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-gray-500 p-1 text-white text-xs rounded">任意</span>
                                <label class="font-bold" for="layout">間取り</label>
                            </div>
                            <div class="grid gap-4">
                                <div class="custom-select-box w-[15rem] bg-white border border-gray-300 rounded-md">
                                    <select name="layout" id="layout" class="w-full p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">--選択してください--</option>
                                        <option value="1R">1R</option>
                                        <option value="1K/DK">1K/DK</option>
                                        <option value="1LK/LDK">1LK/LDK</option>
                                        <option value="2K/DK">2K/DK</option>
                                        <option value="2LK/LDK">2LK/LDK</option>
                                        <option value="3K/DK">3K/DK</option>
                                        <option value="3LK/LDK">3LK/LDK</option>
                                        <option value="4K/DK">4K/DK</option>
                                        <option value="4LK/LDK">4LK/LDK</option>
                                        <option value="5K/DK">5K/DK</option>
                                        <option value="5LK/LDK">5LK/LDK</option>
                                        <option value="6K/DK">6K/DK</option>
                                        <option value="6LK/LDK以上">6LK/LDK以上</option>
                                    </select>
                                </div>
                                <p class="text-red-600 text-sm">近い間取りでかまいません</p>
                            </div>
                        </div>

                        <!-- Area -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-gray-500 p-1 text-white text-xs rounded">任意</span>
                                <label class="font-bold" for="area">専有面積</label>
                            </div>
                            <div class="grid gap-4">
                                <div class="custom-select-box w-[15rem] bg-white border border-gray-300 rounded-md">
                                    <select name="area" id="area" class="w-full p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">--選択してください--</option>
                                        <option value="0">わからない</option>
                                        <option value="10㎡ (3坪)">10㎡ (3坪)</option>
                                        <option value="20㎡ (6.1坪)">20㎡ (6.1坪)</option>
                                        <option value="30㎡ (9.1坪)">30㎡ (9.1坪)</option>
                                        <option value="40㎡ (12.1坪)">40㎡ (12.1坪)</option>
                                        <option value="50㎡ (15.1坪)">50㎡ (15.1坪)</option>
                                        <option value="60㎡ (18.2坪)">60㎡ (18.2坪)</option>
                                        <option value="70㎡ (21.2坪)">70㎡ (21.2坪)</option>
                                        <option value="80㎡ (24.2坪)">80㎡ (24.2坪)</option>
                                        <option value="90㎡ (27.2坪)">90㎡ (27.2坪)</option>
                                        <option value="100㎡ (30.3坪)">100㎡ (30.3坪)</option>
                                        <option value="150㎡ (45.4坪)">150㎡ (45.4坪)</option>
                                        <option value="200㎡ (60.5坪)">200㎡ (60.5坪)</option>
                                        <option value="300㎡ (90.8坪)">300㎡ (90.8坪)</option>
                                        <option value="500㎡ (151.3坪) 以上">500㎡ (151.3坪) 以上</option>
                                    </select>
                                </div>
                                <p class="text-red-600 text-sm">おおよその面積でかまいません</p>
                            </div>
                        </div>

                        <!-- Construction Year -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-red-500 p-1 text-white text-xs rounded">必須</span>
                                <label class="font-bold" for="construction_year">築年</label>
                            </div>
                            <div class="grid gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="custom-select-box w-[15rem] bg-white border border-gray-300 rounded-md">
                                        <select name="construction_year" id="construction_year" required 
                                                class="w-full p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">--選択してください--</option>
                                        </select>
                                    </div>
                                    <span>頃</span>
                                </div>
                                <p class="text-red-600 text-sm">おおよその時期でかまいません</p>
                            </div>
                        </div>

                        <!-- Current Status -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-red-500 p-1 text-white text-xs rounded">必須</span>
                                <label class="font-bold">現状</label>
                            </div>
                            <div class="grid gap-4">
                                <div class="flex gap-4 items-center">
                                    <input type="radio" name="current_status" value="ご自身またはご家族・親戚が居住中" 
                                           id="status_living" required class="focus:ring-2 focus:ring-blue-500">
                                    <label for="status_living">ご自身またはご家族・親族が居住中</label>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <input type="radio" name="current_status" value="賃貸中" 
                                           id="status_rental" required class="focus:ring-2 focus:ring-blue-500">
                                    <label for="status_rental">賃貸中</label>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <input type="radio" name="current_status" value="空き家" 
                                           id="status_vacant" required class="focus:ring-2 focus:ring-blue-500">
                                    <label for="status_vacant">空き家</label>
                                </div>
                            </div>
                        </div>

                        <!-- Relationship -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-red-500 p-1 text-white text-xs rounded">必須</span>
                                <label class="font-bold">あなたと売却物件との関係</label>
                            </div>
                            <div class="grid gap-4">
                                <div class="flex gap-4 items-center">
                                    <input type="radio" name="relationship" value="名義人" 
                                           id="rel_owner" required class="focus:ring-2 focus:ring-blue-500">
                                    <label for="rel_owner">名義人</label>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <input type="radio" name="relationship" value="名義人に売却の同意を得た家族、親族" 
                                           id="rel_family" required class="focus:ring-2 focus:ring-blue-500">
                                    <label for="rel_family">名義人に売却の同意を得た家族、親族</label>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <input type="radio" name="relationship" value="共有名義" 
                                           id="rel_joint" required class="focus:ring-2 focus:ring-blue-500">
                                    <label for="rel_joint">共有名義</label>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <input type="radio" name="relationship" value="会社名義" 
                                           id="rel_company" required class="focus:ring-2 focus:ring-blue-500">
                                    <label for="rel_company">会社名義</label>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <input type="radio" name="relationship" value="弁護士、銀行担当者など、名義人・名義人の家族、親族から依頼を受けた方" 
                                           id="rel_agent" required class="focus:ring-2 focus:ring-blue-500">
                                    <label for="rel_agent">弁護士、銀行担当者など、名義人・名義人の家族、親族から依頼を受けた方</label>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <input type="radio" name="relationship" value="その他" 
                                           id="rel_other" required class="focus:ring-2 focus:ring-blue-500">
                                    <label for="rel_other">その他</label>
                                </div>
                            </div>
                        </div>

                        <!-- Loan Balance -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-gray-500 p-1 text-white text-xs rounded">任意</span>
                                <label class="font-bold" for="loan_balance">住宅ローン残高(残債)</label>
                            </div>
                            <div class="flex items-center gap-4">
                                <span>約</span>
                                <input type="number" name="loan_balance" id="loan_balance" min="0" 
                                       class="flex-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="1000">
                                <span class="w-[3rem]">万円</span>
                            </div>
                        </div>

                        <!-- Desired Price -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-gray-500 p-1 text-white text-xs rounded">任意</span>
                                <label class="font-bold" for="desired_price">希望買取金額</label>
                            </div>
                            <div class="flex items-center gap-4">
                                <span>約</span>
                                <input type="number" name="desired_price" id="desired_price" min="0" 
                                       class="flex-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="2000">
                                <span class="w-[3rem]">万円</span>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-red-500 p-1 text-white text-xs rounded">必須</span>
                                <label class="font-bold" for="name">お名前</label>
                            </div>
                            <div class="grid gap-4">
                                <p class="text-red-600 text-sm">匿名での依頼は承れません</p>
                                <p class="text-gray-600 text-sm">例：売却 太郎</p>
                                <input type="text" name="name" id="name" required maxlength="100"
                                       class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="お名前を入力してください">
                            </div>
                        </div>

                        <!-- Furigana -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-red-500 p-1 text-white text-xs rounded">必須</span>
                                <label class="font-bold" for="furigana">フリガナ</label>
                            </div>
                            <div class="grid gap-4">
                                <p class="text-gray-600 text-sm">例：バイキャク タロウ</p>
                                <input type="text" name="furigana" id="furigana" required maxlength="100"
                                       class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="フリガナを入力してください">
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-red-500 p-1 text-white text-xs rounded">必須</span>
                                <label class="font-bold">性別</label>
                            </div>
                            <div class="grid gap-4">
                                <div class="flex gap-4 items-center">
                                    <input type="radio" name="gender" id="gender_male" value="男性" required 
                                           class="focus:ring-2 focus:ring-blue-500">
                                    <label for="gender_male">男性</label>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <input type="radio" name="gender" id="gender_female" value="女性" required 
                                           class="focus:ring-2 focus:ring-blue-500">
                                    <label for="gender_female">女性</label>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <input type="radio" name="gender" id="gender_no_answer" value="回答しない" required 
                                           class="focus:ring-2 focus:ring-blue-500">
                                    <label for="gender_no_answer">回答しない</label>
                                </div>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-red-500 p-1 text-white text-xs rounded">必須</span>
                                <label class="font-bold" for="phone">電話番号</label>
                            </div>
                            <div class="grid gap-4">
                                <p class="text-red-600 text-sm">番号の間違いがないようご確認ください</p>
                                <p class="text-gray-600 text-sm">例: 0312340000</p>
                                <input type="tel" name="phone" id="phone" required pattern="[0-9]{10,11}"
                                       class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="電話番号を入力してください">
                                
                                <div class="flex gap-4 items-center">
                                    <label for="contact_time" class="text-sm font-medium">ご希望の連絡時間帯</label>
                                    <div class="custom-select-box w-[12rem] bg-white border border-gray-300 rounded-md">
                                        <select name="contact_time" id="contact_time" 
                                                class="w-full p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="9:00 - 12:00">9:00 - 12:00</option>
                                            <option value="12:00 - 15:00">12:00 - 15:00</option>
                                            <option value="15:00 - 18:00">15:00 - 18:00</option>
                                            <option value="18:00 - 21:00">18:00 - 21:00</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-red-500 p-1 text-white text-xs rounded">必須</span>
                                <label class="font-bold" for="email">メールアドレス</label>
                            </div>
                            <div class="grid gap-4">
                                <div class="grid gap-4">
                                    <p class="text-red-600 text-sm">メールアドレスの間違いがないようご確認ください</p>
                                    <p class="text-gray-600 text-sm">例：baikyaku_t@realestate.co.jp<br class="sm:hidden block">PC、携帯どちらも可</p>
                                    <input type="email" name="email" id="email" required maxlength="255"
                                           class="p-3 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="メールアドレスを入力してください">
                                </div>
                                <div class="grid gap-4">
                                    <label for="email_confirm" class="text-sm font-medium">メールアドレス（確認用）</label>
                                    <input type="email" name="email_confirm" id="email_confirm" required maxlength="255"
                                           class="p-3 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="確認用メールアドレスを入力してください">
                                    <p class="hidden text-red-400 text-sm" id="email-confirmation-warning">アドレスが異なります</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Method -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <span class="bg-gray-500 p-1 text-white text-xs rounded">任意</span>
                                <label class="font-bold">希望する連絡方法</label>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex gap-2 items-center">
                                    <input type="checkbox" name="contact_method_phone" value="電話" 
                                           id="contact_phone" class="focus:ring-2 focus:ring-blue-500">
                                    <label for="contact_phone">電話</label>
                                </div>
                                <div class="flex gap-2 items-center">
                                    <input type="checkbox" name="contact_method_email" value="メール" 
                                           id="contact_email" class="focus:ring-2 focus:ring-blue-500">
                                    <label for="contact_email">メール</label>
                                </div>
                            </div>
                        </div>

                        <!-- Assessment Method -->
                        <div class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-4 md:px-8">
                            <div class="flex items-center gap-4">
                                <span class="bg-gray-500 p-1 text-white text-xs rounded">任意</span>
                                <label class="font-bold">希望査定方法</label>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex gap-2 items-center">
                                    <input type="checkbox" name="assessment_method_desk" value="机上" 
                                           id="assessment_desk" class="focus:ring-2 focus:ring-blue-500">
                                    <label for="assessment_desk">机上</label>
                                </div>
                                <div class="flex gap-2 items-center">
                                    <input type="checkbox" name="assessment_method_visit" value="訪問" 
                                           id="assessment_visit" class="focus:ring-2 focus:ring-blue-500">
                                    <label for="assessment_visit">訪問</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <button type="submit" name="send" 
                                class="px-8 py-4 bg-blue-600 hover:bg-blue-700 transition-all text-white text-xl font-bold rounded-lg shadow-lg focus:ring-4 focus:ring-blue-300">
                            送信する
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer class="footer bg-gray-900 text-white mt-16">
        <div class="footer__inner p-4 md:p-8">
            <div class="flex flex-col md:flex-row justify-center md:justify-between items-center gap-8 text-sm h-36">
                <ul class="flex gap-4">
                    <li><a href="#" class="hover:text-blue-300 transition-colors">不動産売却</a></li>
                    <li><a href="#" class="hover:text-blue-300 transition-colors">不動産購入</a></li>
                    <li><a href="#" class="hover:text-blue-300 transition-colors">お問い合わせ</a></li>
                    <li><a href="#" class="hover:text-blue-300 transition-colors">加盟店募集</a></li>
                </ul>
                <ul class="flex gap-4">
                    <li><a href="#" class="hover:text-blue-300 transition-colors">プライバシーポリシー</a></li>
                    <li><a href="#" class="hover:text-blue-300 transition-colors">ご利用規約</a></li>
                </ul>
            </div>
        </div>
        <div class="flex justify-center py-4 border-t border-gray-700 w-full">
            <p class="text-xs">&copy; <?php echo date('Y'); ?> Real Estate All Rights Reserved.</p>
        </div>
    </footer>

    <button id="backToTop" class="fixed bottom-7 md:bottom-10 right-7 md:right-10 text-white hidden w-10 md:w-14 h-10 md:h-14 bg-blue-600 rounded-full shadow-lg hover:bg-blue-700 transition-all">
        <img class="opacity-75 hover:opacity-100 transition-all" src="../assets/img/backtotop-icon.png" alt="トップに戻る">
    </button>

    <script src="../assets/js/script.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            offset: 0,
            once: true,
        });

        // Form validation
        document.getElementById('inquiriesForm').addEventListener('submit', function(event) {
            const email = document.getElementById('email');
            const confirmEmail = document.getElementById('email_confirm');
            const warning = document.getElementById('email-confirmation-warning');

            if (email.value !== confirmEmail.value) {
                warning.classList.remove('hidden');
                event.preventDefault();
                confirmEmail.focus();
                return false;
            } else {
                warning.classList.add('hidden');
            }
        });

        // Email confirmation validation
        document.getElementById('email_confirm').addEventListener('input', function() {
            const email = document.getElementById('email');
            const confirmEmail = this;
            const warning = document.getElementById('email-confirmation-warning');

            if (email.value !== confirmEmail.value && confirmEmail.value !== '') {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        });

        // Generate construction year options
        function generateYearOptions() {
            const currentYear = new Date().getFullYear();
            const yearSelect = document.getElementById('construction_year');

            const eras = [
                { name: '令和', start: 2019, offset: 2018 },
                { name: '平成', start: 1989, offset: 1988 },
                { name: '昭和', start: 1926, offset: 1925 }
            ];

            function getJapaneseYear(year) {
                for (let era of eras) {
                    if (year >= era.start) {
                        const eraYear = year - era.offset;
                        return year + "年(" + era.name + (eraYear === 1 ? '元' : eraYear + '年') + ")";
                    }
                }
                return year + "年";
            }

            for (let i = currentYear; i >= 1926; i--) {
                const japaneseYear = getJapaneseYear(i);
                const age = currentYear - i;
                const value = japaneseYear + "築" + (age === 0 ? "今年" : age + "年");

                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                yearSelect.appendChild(option);
            }
        }

        generateYearOptions();
    </script>
</body>
</html>