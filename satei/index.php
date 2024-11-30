<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $物件の種別 = isset($_POST['物件の種別']) ? htmlspecialchars($_POST['物件の種別'], ENT_QUOTES, 'UTF-8') : '';
  $prefecture = isset($_POST['prefecture']) ? htmlspecialchars($_POST['prefecture'], ENT_QUOTES, 'UTF-8') : '';
  $city = isset($_POST['city']) ? htmlspecialchars($_POST['city'], ENT_QUOTES, 'UTF-8') : '';
  $town = isset($_POST['town']) ? htmlspecialchars($_POST['town'], ENT_QUOTES, 'UTF-8') : '';

  // Store sanitized data in session variables
  $_SESSION['物件の種別'] = $物件の種別;
  $_SESSION['prefecture'] = $prefecture;
  $_SESSION['city'] = $city;
  $_SESSION['town'] = $town;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
  <title>査定</title>
  <link rel="icon" type="image/png" href="../assets/img/site-favicon.png">
</head>

<body data-aos="custom-fadeUp">
  <header class="flex text-white justify-between items-center transition-colors p-4 md:px-10 w-full sticky top-0 z-10">
    <a href="../baikyaku/index.html"><img src="../assets/img/real-estate-logo.png" class="h-8 sm:h-9 w-auto" alt /></a>
  </header>
  <main>
    <section class="form-section flex justify-center items-center py-16">
      <div class="form-section__inner w-full max-w-[1040px] h-auto grid gap-6 px-4">
        <h1 class="text-3xl text-center font-bold">査定</h1>
        <form class="grid gap-6" action="mail.php" method="post" id="inquiriesForm" onsubmit="submitForm(event)">
          <table class="grid border-0 md:border-[1px] border-black">
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] gap-4 p-4 px-0 md:px-8">
              <th class="flex items-center gap-4">
                <p class="bg-red-500 p-1 text-white">必須</p>
                <p class="font-bold">物件種別</p>
              </th>
              <td class="flex justify-start items-center">
                <?php
                if ($物件の種別) {
                  echo "<p>$物件の種別</p>";
                } else {
                  echo "";
                }
                ?>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-red-500 p-1 text-white">必須</p>
                <p class="font-bold">所在地</p>
              </th>
              <td class="grid gap-4">
                <div>
                  <?php
                  echo "<strong>$prefecture$city$town</strong>";
                  ?>
                </div>
                <p>丁目・番地・号 (入力例: 1-3-13)</p>
                <input type="text" name="丁目" class="w-full bg-[#C2C2C2]" />
                <p>マンション名</p>
                <input type="text" name="マンション名" class="w-full bg-[#C2C2C2]" />
                <p>号室</p>
                <input type="text" name="号室" class="w-full bg-[#C2C2C2]" />
                <p class="text-[#5D0000]">
                  番地など住所情報にお間違えがないかご確認ください（不足していると、査定が実施できません）
                </p>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th
                class="flex items-center gap-4">
                <p class="bg-gray-500 p-1 text-white">任意</p>
                <p class="font-bold">間取り</p>
              </th>
              <td class="grid gap-4">
                <div class="custom-select-box w-[15rem] bg-[#C2C2C2]">
                  <select name="間取り">
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
                <p class="text-[#5D0000]">近い間取りでかまいません</p>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-gray-500 p-1 text-white">任意</p>
                <p class="font-bold">専有面積</p>
              </th>
              <td class="grid gap-4">
                <div class="custom-select-box w-[15rem] bg-[#C2C2C2]">
                  <select name="専有面積">
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
                    <option value="110㎡ (33.3坪)">110㎡ (33.3坪)</option>
                    <option value="120㎡ (36.3坪)">120㎡ (36.3坪)</option>
                    <option value="130㎡ (39.3坪)">130㎡ (39.3坪)</option>
                    <option value="140㎡ (42.4坪)">140㎡ (42.4坪)</option>
                    <option value="150㎡ (45.4坪)">150㎡ (45.4坪)</option>
                    <option value="160㎡ (48.4坪)">160㎡ (48.4坪)</option>
                    <option value="170㎡ (51.4坪)">170㎡ (51.4坪)</option>
                    <option value="180㎡ (54.5坪)">180㎡ (54.5坪)</option>
                    <option value="190㎡ (57.5坪)">190㎡ (57.5坪)</option>
                    <option value="200㎡ (60.5坪)">200㎡ (60.5坪)</option>
                    <option value="210㎡ (63.5坪)">210㎡ (63.5坪)</option>
                    <option value="220㎡ (66.6坪)">220㎡ (66.6坪)</option>
                    <option value="230㎡ (69.6坪)">230㎡ (69.6坪)</option>
                    <option value="240㎡ (72.6坪)">240㎡ (72.6坪)</option>
                    <option value="250㎡ (75.6坪)">250㎡ (75.6坪)</option>
                    <option value="260㎡ (78.6坪)">260㎡ (78.6坪)</option>
                    <option value="270㎡ (81.7坪)">270㎡ (81.7坪)</option>
                    <option value="280㎡ (84.7坪)">280㎡ (84.7坪)</option>
                    <option value="290㎡ (87.7坪)">290㎡ (87.7坪)</option>
                    <option value="300㎡ (90.8坪)">300㎡ (90.8坪)</option>
                    <option value="310㎡ (93.8坪)">310㎡ (93.8坪)</option>
                    <option value="320㎡ (96.8坪)">320㎡ (96.8坪)</option>
                    <option value="330㎡ (99.8坪)">330㎡ (99.8坪)</option>
                    <option value="340㎡ (102.9坪)">340㎡ (102.9坪)</option>
                    <option value="350㎡ (105.9坪)">350㎡ (105.9坪)</option>
                    <option value="360㎡ (108.9坪)">360㎡ (108.9坪)</option>
                    <option value="370㎡ (111.9坪)">370㎡ (111.9坪)</option>
                    <option value="380㎡ (115坪)">380㎡ (115坪)</option>
                    <option value="390㎡ (118坪)">390㎡ (118坪)</option>
                    <option value="400㎡ (121坪)">400㎡ (121坪)</option>
                    <option value="410㎡ (124坪)">410㎡ (124坪)</option>
                    <option value="420㎡ (127.1坪)">420㎡ (127.1坪)</option>
                    <option value="430㎡ (130.1坪)">430㎡ (130.1坪)</option>
                    <option value="440㎡ (133.1坪)">440㎡ (133.1坪)</option>
                    <option value="450㎡ (136.1坪)">450㎡ (136.1坪)</option>
                    <option value="460㎡ (139.2坪)">460㎡ (139.2坪)</option>
                    <option value="470㎡ (142.2坪)">470㎡ (142.2坪)</option>
                    <option value="480㎡ (145.2坪)">480㎡ (145.2坪)</option>
                    <option value="490㎡ (148.2坪)">490㎡ (148.2坪)</option>
                    <option value="500㎡ (151.3坪) 以上">500㎡ (151.3坪) 以上</option>
                  </select>
                </div>
                <p class="text-[#5D0000]">おおよその面積でかまいません</p>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-red-500 p-1 text-white">必須</p>
                <p class="font-bold">築年</p>
              </th>
              <td class="grid gap-4">
                <div class="flex items-center gap-4">
                  <div class="custom-select-box w-[15rem] bg-[#C2C2C2]">
                    <select name="築年" id="yearSelect">
                      <option value="">--選択してください--</option>
                    </select>
                  </div>
                  <p>頃</p>
                </div>
                <p class="text-[#5D0000]">おおよその時期でかまいません</p>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-red-500 p-1 text-white">必須</p>
                <p class="font-bold">現状</p>
              </th>
              <td class="grid gap-4">
                <div class="flex gap-4">
                  <input class="bg-[#C2C2C2]" type="radio" name="現状" value="ご自身またはご家族・親戚が居住中" id="居住中" />
                  <label for="居住中">ご自身またはご家族・親族が居住中</label>
                </div>
                <div class="flex gap-4">
                  <input class="bg-[#C2C2C2]" type="radio" name="現状" value="賃貸中" id="賃貸中" /><label for="賃貸中">賃貸中</label>
                </div>
                <div class="flex gap-4">
                  <input class="bg-[#C2C2C2]" type="radio" name="現状" value="空き家" id="空き家" /><label for="空き家">空き家</label>
                </div>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-red-500 p-1 text-white">必須</p>
                <p class="font-bold">あなたと売却物件との関係</p>
              </th>
              <td class="grid gap-4">
                <div class="flex gap-4">
                  <input class="bg-[#C2C2C2]" type="radio" name="あなたと売却物件との関係" value="名義人" id="名義人" /><label for="名義人">名義人</label>
                </div>
                <div class="flex gap-4">
                  <input class="bg-[#C2C2C2]" type="radio" name="あなたと売却物件との関係" value="名義人に売却の同意を得た家族、親族" id="配偶者" /><label for="配偶者">名義人に売却の同意を得た家族、親族</label>
                </div>
                <div class="flex gap-4">
                  <input class="bg-[#C2C2C2]" type="radio" name="あなたと売却物件との関係" value="共有名義" id="共有名義" /><label for="共有名義">共有名義</label>
                </div>
                <div class="flex gap-4">
                  <input class="bg-[#C2C2C2]" type="radio" name="あなたと売却物件との関係" value="会社名義" id="会社名義" /><label for="会社名義">会社名義</label>
                </div>
                <div class="flex gap-4">
                  <input class="bg-[#C2C2C2]" type="radio" name="あなたと売却物件との関係" value="弁護士、銀行担当者など、名義人・名義人の家族、親族から依頼を受けた方" id="代理人" /><label for="代理人">弁護士、銀行担当者など、名義人・名義人の家族、親族から依頼を受けた方</label>
                </div>
                <div class="flex gap-4">
                  <input class="bg-[#C2C2C2]" type="radio" name="あなたと売却物件との関係" value="その他" id="その他" /><label for="その他">その他</label>
                </div>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-gray-500 p-1 text-white">任意</p>
                <p class="font-bold">住宅ローン残高(残債)</p>
              </th>
              <td class="flex items-center gap-4">
                <p>約</p><input type="text" name="住宅ローン残高" class="w-full bg-[#C2C2C2]">
                <p class="w-[3rem]">万円</p>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-gray-500 p-1 text-white">任意</p>
                <p class="font-bold">希望買取金額</p>
              </th>
              <td class="flex items-center gap-4">
                <p>約</p><input type="text" name="希望買取金額" class="w-full bg-[#C2C2C2]">
                <p class="w-[3rem]">万円</p>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-red-500 p-1 text-white">必須</p>
                <p class="font-bold">お名前</p>
              </th>
              <td class="grid gap-4">
                <p class="text-[#5D0000]">匿名での依頼は承れません</p>
                <p>例：売却 太郎</p>
                <input type="text" name="お名前" class="w-full bg-[#C2C2C2]" required />
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-red-500 p-1 text-white">必須</p>
                <p class="font-bold">フリガナ</p>
              </th>
              <td class="grid gap-4">
                <p>例：バイキャク タロウ</p>
                <input type="text" name="フリガナ" class="w-full bg-[#C2C2C2]" required />
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-red-500 p-1 text-white">必須</p>
                <p class="font-bold">性別</p>
              </th>
              <td class="grid gap-4">
                <div class="flex gap-4"><input class="bg-[#C2C2C2]" type="radio" name="性別" id="男性" value="男性"><label for="男性">男性</label></div>
                <div class="flex gap-4"><input class="bg-[#C2C2C2]" type="radio" name="性別" id="女性" value="女性"><label for="女性">女性</label></div>
                <div class="flex gap-4"><input class="bg-[#C2C2C2]" type="radio" name="性別" id="回答しない" value="回答しない"><label for="回答しない">回答しない</label></div>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-red-500 p-1 text-white">必須</p>
                <p class="font-bold">電話番号</p>
              </th>
              <td class="grid gap-4">
                <p class="text-[#5D0000]">番号の間違いがないようご確認ください</p>
                <p>例: 0312340000</p>
                <input type="number" name="電話番号" class="w-full bg-[#C2C2C2]" required>
                <div class="flex gap-4">
                  <p>ご希望の連絡時間帯</p>
                  <div class="custom-select-box w-[12rem] bg-[#C2C2C2]">
                    <select name="ご希望の連絡時間帯">
                      <option value="9:00 - 12:00">9:00 - 12:00</option>
                      <option value="12:00 - 15:00">12:00 - 15:00</option>
                      <option value="15:00 - 18:00">15:00 - 18:00</option>
                      <option value="18:00 - 21:00">18:00 - 21:00</option>
                    </select>
                  </div>
                </div>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-red-500 p-1 text-white">必須</p>
                <p class="font-bold">メールアドレス</p>
              </th>
              <td class="grid gap-4">
                <div class="grid gap-4">
                  <p class="text-[#5D0000]">メールアドレスの間違いがないようご確認ください</p>
                  <p>例：baikyaku_t@realestate.co.jp <br class="sm:hidden block" />PC、携帯どちらも可</p>
                  <input type="email" name="メールアドレス" id="メールアドレス" class="p-[10px] w-full bg-[#C2C2C2]" required />
                </div>
                <div class="grid gap-4">
                  <p>メールアドレス（確認用）</p>
                  <input type="email" name="メールアドレス（確認用）" id="メールアドレス（確認用）" class="p-[10px] w-full bg-[#C2C2C2]" required />
                  <p class="hidden text-red-400" id="email-confirmation-warning">アドレスが異なります</p>
                </div>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-gray-500 p-1 text-white">任意</p>
                <p class="font-bold">希望する連絡方法</p>
              </th>
              <td class="flex gap-4">
                <div class="flex gap-2">
                  <input class="bg-[#C2C2C2]" type="checkbox" name="希望する連絡方法1" value="電話" id="電話" /><label for="電話">電話</label>
                </div>
                <div class="flex gap-2">
                  <input class="bg-[#C2C2C2]" type="checkbox" name="希望する連絡方法2" value="メール" id="メール" /><label for="メール">メール</label>
                </div>
              </td>
            </tr>
            <tr class="grid grid-rows-[auto_auto] md:grid-cols-[17rem_1fr] items-start gap-4 p-4 px-0 md:px-8 border-0 md:border-t-[1px] border-black">
              <th class="flex items-center gap-4">
                <p class="bg-gray-500 p-1 text-white">任意</p>
                <p class="font-bold">希望査定方法</p>
              </th>
              <td class="flex gap-4">
                <div class="flex gap-2">
                  <input class="bg-[#C2C2C2]" type="checkbox" name="希望査定方法1" value="机上" id="机上" /><label for="机上">机上</label>
                </div>
                <div class="flex gap-2">
                  <input class="bg-[#C2C2C2]" type="checkbox" name="希望査定方法2" value="訪問" id="訪問" /><label for="訪問">訪問</label>
                </div>
              </td>
            </tr>
          </table>
          <div class="flex justify-center">
            <button type="submit" name="send" class="px-8 py-4 bg-[#5DADFF] hover:bg-[#0060c3] transition-all text-white text-xl font-bold">
              送信する
            </button>
          </div>
        </form>
      </div>
    </section>
  </main>
  <footer class="footer bg-[#00152A] grid text-white mt-16">
    <div class="footer__inner p-4 md:p-8">
      <div class="flex flex-col md:flex-row justify-center md:justify-between items-center gap-8 text-[12px] md:text-sm h-36">
        <ul class="flex gap-4" data-aos="fade-up">
          <li><a href="#">不動産売却</a></li>
          <li><a href="#">不動産購入</a></li>
          <li><a href="#">お問い合わせ</a></li>
          <li><a href="#">加盟店募集</a></li>
        </ul>
        <ul class="flex gap-4" data-aos="fade-up">
          <li><a href="#">プライバシーポリシー</a></li>
          <li><a href="#">ご利用規約</a></li>
        </ul>
      </div>
    </div>
    <div class="flex justify-center py-4 border-t-[1px] w-full">
      <p class="text-xs">© 2024 Real Estate All Rights Reserved.</p>
    </div>
  </footer>
  <button id="backToTop" class="fixed bottom-7 md:bottom-10 right-7 md:right-10 text-white hidden w-10 md:w-14 h-10 md:h-14">
    <img class="opacity-75 hover:opacity-100 transition-all" src="../assets/img/backtotop-icon.png" />
  </button>
  <script src="../assets/js/script.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 1000,
      offset: 0,
      once: true,
    });

    //loading warning message when the email is not matching
    function submitForm(event) {

      let form = document.getElementById("inquiriesForm");

      let email = document.getElementById("メールアドレス");
      let confirmEmail = document.getElementById("メールアドレス（確認用）");

      if (form.checkValidity()) {
        if (email.value === confirmEmail.value) {

          document.getElementById("email-confirmation-warning").classList.add('hidden');
        } else {
          document.getElementById("email-confirmation-warning").classList.remove('hidden');
          event.preventDefault();
        }
      } else {
        form.reportValidity();
        event.preventDefault();
      }
    }

    //aos timing
    document.querySelectorAll('[data-aos="fade-up"]').forEach((element, index) => {
      element.setAttribute('data-aos-delay', `${index * 100}`);
    });

    // Auto update Year build and add new year as it reach
    const currentYear = new Date().getFullYear();

    const eras = [{
        name: '令和',
        start: 2019,
        offset: 2018
      },
      {
        name: '平成',
        start: 1989,
        offset: 1988
      },
      {
        name: '昭和',
        start: 1926,
        offset: 1925
      },
    ];

    function getJapaneseYear(year) {
      for (let era of eras) {
        if (year >= era.start) {
          const eraYear = year - era.offset;
          return year + "年(" + era.name + (eraYear == 1 ? '元' : eraYear + '年') + ")";
        }
      }
      return year + "年";
    }

    function generateYearOptions() {
      const yearSelect = document.getElementById('yearSelect');

      for (let i = currentYear; i >= 1926; i--) {
        const japaneseYear = getJapaneseYear(i);
        const age = currentYear - i;
        const value = japaneseYear + "築" + (age == 0 ? "今年" : age + "年");

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