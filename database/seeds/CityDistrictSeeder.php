
<?php

use App\City;
use App\District;
use Illuminate\Database\Seeder;

class CityDistrictSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$cities = [
			['name' => '台北市', 'code' => 'A', 'telcode' => '02', 'districts' => [['name' => '中正區', 'zipcode' => '100'], ['name' => '大同區', 'zipcode' => '103'], ['name' => '中山區', 'zipcode' => '104'], ['name' => '松山區', 'zipcode' => '105'], ['name' => '大安區', 'zipcode' => '106'], ['name' => '萬華區', 'zipcode' => '108'], ['name' => '信義區', 'zipcode' => '110'], ['name' => '士林區', 'zipcode' => '111'], ['name' => '北投區', 'zipcode' => '112'], ['name' => '內湖區', 'zipcode' => '114'], ['name' => '南港區', 'zipcode' => '115'], ['name' => '文山區', 'zipcode' => '116']]], ['name' => '基隆市', 'code' => 'C', 'telcode' => '02', 'districts' => [['name' => '仁愛區', 'zipcode' => '200'], ['name' => '信義區', 'zipcode' => '201'], ['name' => '中正區', 'zipcode' => '202'], ['name' => '中山區', 'zipcode' => '203'], ['name' => '安樂區', 'zipcode' => '204'], ['name' => '暖暖區', 'zipcode' => '205'], ['name' => '七堵區', 'zipcode' => '206']]], ['name' => '新北市', 'code' => 'F', 'telcode' => '02', 'districts' => [['name' => '萬里區', 'zipcode' => '207'], ['name' => '金山區', 'zipcode' => '208'], ['name' => '板橋區', 'zipcode' => '220'], ['name' => '汐止區', 'zipcode' => '221'], ['name' => '深坑區', 'zipcode' => '222'], ['name' => '石碇區', 'zipcode' => '223'], ['name' => '瑞芳區', 'zipcode' => '224'], ['name' => '平溪區', 'zipcode' => '226'], ['name' => '雙溪區', 'zipcode' => '227'], ['name' => '貢寮區', 'zipcode' => '228'], ['name' => '新店區', 'zipcode' => '231'], ['name' => '坪林區', 'zipcode' => '232'], ['name' => '烏來區', 'zipcode' => '233'], ['name' => '永和區', 'zipcode' => '234'], ['name' => '中和區', 'zipcode' => '235'], ['name' => '土城區', 'zipcode' => '236'], ['name' => '三峽區', 'zipcode' => '237'], ['name' => '樹林區', 'zipcode' => '238'], ['name' => '鶯歌區', 'zipcode' => '239'], ['name' => '三重區', 'zipcode' => '241'], ['name' => '新莊區', 'zipcode' => '242'], ['name' => '泰山區', 'zipcode' => '243'], ['name' => '林口區', 'zipcode' => '244'], ['name' => '蘆洲區', 'zipcode' => '247'], ['name' => '五股區', 'zipcode' => '248'], ['name' => '八里區', 'zipcode' => '249'], ['name' => '淡水區', 'zipcode' => '251'], ['name' => '三芝區', 'zipcode' => '252'], ['name' => '石門區', 'zipcode' => '253']]], ['name' => '宜蘭縣', 'code' => 'G', 'telcode' => '03', 'districts' => [['name' => '宜蘭市', 'zipcode' => '260'], ['name' => '頭城鎮', 'zipcode' => '261'], ['name' => '礁溪鄉', 'zipcode' => '262'], ['name' => '壯圍鄉', 'zipcode' => '263'], ['name' => '員山鄉', 'zipcode' => '264'], ['name' => '羅東鎮', 'zipcode' => '265'], ['name' => '三星鄉', 'zipcode' => '266'], ['name' => '大同鄉', 'zipcode' => '267'], ['name' => '五結鄉', 'zipcode' => '268'], ['name' => '冬山鄉', 'zipcode' => '269'], ['name' => '蘇澳鎮', 'zipcode' => '270'], ['name' => '南澳鄉', 'zipcode' => '272']]], ['name' => '新竹市', 'code' => 'O', 'telcode' => '03', 'districts' => [['name' => '東區', 'zipcode' => '300'], ['name' => '北區', 'zipcode' => '300'], ['name' => '香山區', 'zipcode' => '300']]], ['name' => '新竹縣', 'code' => 'J', 'telcode' => '03', 'districts' => [['name' => '竹北市', 'zipcode' => '302'], ['name' => '湖口鄉', 'zipcode' => '303'], ['name' => '新豐鄉', 'zipcode' => '304'], ['name' => '新埔鎮', 'zipcode' => '305'], ['name' => '關西鎮', 'zipcode' => '306'], ['name' => '芎林鄉', 'zipcode' => '307'], ['name' => '寶山鄉', 'zipcode' => '308'], ['name' => '竹東鎮', 'zipcode' => '310'], ['name' => '五峰鄉', 'zipcode' => '311'], ['name' => '橫山鄉', 'zipcode' => '312'], ['name' => '尖石鄉', 'zipcode' => '313'], ['name' => '北埔鄉', 'zipcode' => '314'], ['name' => '峨眉鄉', 'zipcode' => '315']]], ['name' => '桃園縣', 'code' => 'H', 'telcode' => '03', 'districts' => [['name' => '中壢市', 'zipcode' => '320'], ['name' => '平鎮市', 'zipcode' => '324'], ['name' => '龍潭鄉', 'zipcode' => '325'], ['name' => '楊梅鎮', 'zipcode' => '326'], ['name' => '新屋鄉', 'zipcode' => '327'], ['name' => '觀音鄉', 'zipcode' => '328'], ['name' => '桃園市', 'zipcode' => '330'], ['name' => '龜山鄉', 'zipcode' => '333'], ['name' => '八德市', 'zipcode' => '334'], ['name' => '大溪鎮', 'zipcode' => '335'], ['name' => '復興鄉', 'zipcode' => '336'], ['name' => '大園鄉', 'zipcode' => '337'], ['name' => '蘆竹鄉', 'zipcode' => '338']]], ['name' => '苗栗縣', 'code' => 'K', 'telcode' => '037', 'districts' => [['name' => '竹南鎮', 'zipcode' => '350'], ['name' => '頭份鎮', 'zipcode' => '351'], ['name' => '三灣鄉', 'zipcode' => '352'], ['name' => '南庄鄉', 'zipcode' => '353'], ['name' => '獅潭鄉', 'zipcode' => '354'], ['name' => '後龍鎮', 'zipcode' => '356'], ['name' => '通霄鎮', 'zipcode' => '357'], ['name' => '苑裡鎮', 'zipcode' => '358'], ['name' => '苗栗市', 'zipcode' => '360'], ['name' => '造橋鄉', 'zipcode' => '361'], ['name' => '頭屋鄉', 'zipcode' => '362'], ['name' => '公館鄉', 'zipcode' => '363'], ['name' => '大湖鄉', 'zipcode' => '364'], ['name' => '泰安鄉', 'zipcode' => '365'], ['name' => '銅鑼鄉', 'zipcode' => '366'], ['name' => '三義鄉', 'zipcode' => '367'], ['name' => '西湖鄉', 'zipcode' => '368'], ['name' => '卓蘭鎮', 'zipcode' => '369']]], ['name' => '臺中市', 'code' => 'B', 'telcode' => '04', 'districts' => [['name' => '中區', 'zipcode' => '400'], ['name' => '東區', 'zipcode' => '401'], ['name' => '南區', 'zipcode' => '402'], ['name' => '西區', 'zipcode' => '403'], ['name' => '北區', 'zipcode' => '404'], ['name' => '北屯區', 'zipcode' => '406'], ['name' => '西屯區', 'zipcode' => '407'], ['name' => '南屯區', 'zipcode' => '408'], ['name' => '太平區', 'zipcode' => '411'], ['name' => '大里區', 'zipcode' => '412'], ['name' => '霧峰區', 'zipcode' => '413'], ['name' => '烏日區', 'zipcode' => '414'], ['name' => '豐原區', 'zipcode' => '420'], ['name' => '后里區', 'zipcode' => '421'], ['name' => '石岡區', 'zipcode' => '422'], ['name' => '東勢區', 'zipcode' => '423'], ['name' => '和平區', 'zipcode' => '424'], ['name' => '新社區', 'zipcode' => '426'], ['name' => '潭子區', 'zipcode' => '427'], ['name' => '大雅區', 'zipcode' => '428'], ['name' => '神岡區', 'zipcode' => '429'], ['name' => '大肚區', 'zipcode' => '432'], ['name' => '沙鹿區', 'zipcode' => '433'], ['name' => '龍井區', 'zipcode' => '434'], ['name' => '梧棲區', 'zipcode' => '435'], ['name' => '清水區', 'zipcode' => '436'], ['name' => '大甲區', 'zipcode' => '437'], ['name' => '外埔區', 'zipcode' => '438'], ['name' => '大安區', 'zipcode' => '439']]], ['name' => '彰化縣', 'code' => 'N', 'telcode' => '04', 'districts' => [['name' => '彰化市', 'zipcode' => '500'], ['name' => '芬園鄉', 'zipcode' => '502'], ['name' => '花壇鄉', 'zipcode' => '503'], ['name' => '秀水鄉', 'zipcode' => '504'], ['name' => '鹿港鎮', 'zipcode' => '505'], ['name' => '福興鄉', 'zipcode' => '506'], ['name' => '線西鄉', 'zipcode' => '507'], ['name' => '和美鎮', 'zipcode' => '508'], ['name' => '伸港鄉', 'zipcode' => '509'], ['name' => '員林鎮', 'zipcode' => '510'], ['name' => '社頭鄉', 'zipcode' => '511'], ['name' => '永靖鄉', 'zipcode' => '512'], ['name' => '埔心鄉', 'zipcode' => '513'], ['name' => '溪湖鎮', 'zipcode' => '514'], ['name' => '大村鄉', 'zipcode' => '515'], ['name' => '埔鹽鄉', 'zipcode' => '516'], ['name' => '田中鎮', 'zipcode' => '520'], ['name' => '北斗鎮', 'zipcode' => '521'], ['name' => '田尾鄉', 'zipcode' => '522'], ['name' => '埤頭鄉', 'zipcode' => '523'], ['name' => '溪州鄉', 'zipcode' => '524'], ['name' => '竹塘鄉', 'zipcode' => '525'], ['name' => '二林鎮', 'zipcode' => '526'], ['name' => '大城鄉', 'zipcode' => '527'], ['name' => '芳苑鄉', 'zipcode' => '528'], ['name' => '二水鄉', 'zipcode' => '530']]], ['name' => '南投縣', 'code' => 'M', 'telcode' => '049', 'districts' => [['name' => '南投市', 'zipcode' => '540'], ['name' => '中寮鄉', 'zipcode' => '541'], ['name' => '草屯鎮', 'zipcode' => '542'], ['name' => '國姓鄉', 'zipcode' => '544'], ['name' => '埔里鎮', 'zipcode' => '545'], ['name' => '仁愛鄉', 'zipcode' => '546'], ['name' => '名間鄉', 'zipcode' => '551'], ['name' => '集集鎮', 'zipcode' => '552'], ['name' => '水里鄉', 'zipcode' => '553'], ['name' => '魚池鄉', 'zipcode' => '555'], ['name' => '信義鄉', 'zipcode' => '556'], ['name' => '竹山鎮', 'zipcode' => '557'], ['name' => '鹿谷鄉', 'zipcode' => '558']]], ['name' => '嘉義縣', 'code' => 'Q', 'telcode' => '05', 'districts' => [['name' => '嘉義市', 'zipcode' => '600'], ['name' => '番路鄉', 'zipcode' => '602'], ['name' => '梅山鄉', 'zipcode' => '603'], ['name' => '竹崎鄉', 'zipcode' => '604'], ['name' => '阿里山鄉', 'zipcode' => '605'], ['name' => '中埔鄉', 'zipcode' => '606'], ['name' => '大埔鄉', 'zipcode' => '607'], ['name' => '水上鄉', 'zipcode' => '608'], ['name' => '鹿草鄉', 'zipcode' => '611'], ['name' => '太保市', 'zipcode' => '612'], ['name' => '朴子市', 'zipcode' => '613'], ['name' => '東石鄉', 'zipcode' => '614'], ['name' => '六腳鄉', 'zipcode' => '615'], ['name' => '新港鄉', 'zipcode' => '616'], ['name' => '民雄鄉', 'zipcode' => '621'], ['name' => '大林鎮', 'zipcode' => '622'], ['name' => '溪口鄉', 'zipcode' => '623'], ['name' => '義竹鄉', 'zipcode' => '624'], ['name' => '布袋鎮', 'zipcode' => '625']]], ['name' => '雲林縣', 'code' => 'P', 'telcode' => '05', 'districts' => [['name' => '斗南鎮', 'zipcode' => '630'], ['name' => '大埤鄉', 'zipcode' => '631'], ['name' => '虎尾鎮', 'zipcode' => '632'], ['name' => '土庫鎮', 'zipcode' => '633'], ['name' => '褒忠鄉', 'zipcode' => '634'], ['name' => '東勢鄉', 'zipcode' => '635'], ['name' => '臺西鄉', 'zipcode' => '636'], ['name' => '崙背鄉', 'zipcode' => '637'], ['name' => '麥寮鄉', 'zipcode' => '638'], ['name' => '斗六市', 'zipcode' => '640'], ['name' => '林內鄉', 'zipcode' => '643'], ['name' => '古坑鄉', 'zipcode' => '646'], ['name' => '莿桐鄉', 'zipcode' => '647'], ['name' => '西螺鎮', 'zipcode' => '648'], ['name' => '二崙鄉', 'zipcode' => '649'], ['name' => '北港鎮', 'zipcode' => '651'], ['name' => '水林鄉', 'zipcode' => '652'], ['name' => '口湖鄉', 'zipcode' => '653'], ['name' => '四湖鄉', 'zipcode' => '654'], ['name' => '元長鄉', 'zipcode' => '655']]], ['name' => '臺南市', 'code' => 'D', 'telcode' => '06', 'districts' => [['name' => '中西區', 'zipcode' => '700'], ['name' => '東區', 'zipcode' => '701'], ['name' => '南區', 'zipcode' => '702'], ['name' => '北區', 'zipcode' => '704'], ['name' => '安平區', 'zipcode' => '708'], ['name' => '安南區', 'zipcode' => '709'], ['name' => '永康區', 'zipcode' => '710'], ['name' => '歸仁區', 'zipcode' => '711'], ['name' => '新化區', 'zipcode' => '712'], ['name' => '左鎮區', 'zipcode' => '713'], ['name' => '玉井區', 'zipcode' => '714'], ['name' => '楠西區', 'zipcode' => '715'], ['name' => '南化區', 'zipcode' => '716'], ['name' => '仁德區', 'zipcode' => '717'], ['name' => '關廟區', 'zipcode' => '718'], ['name' => '龍崎區', 'zipcode' => '719'], ['name' => '官田區', 'zipcode' => '720'], ['name' => '麻豆區', 'zipcode' => '721'], ['name' => '佳里區', 'zipcode' => '722'], ['name' => '西港區', 'zipcode' => '723'], ['name' => '七 股區', 'zipcode' => '724'], ['name' => '將軍區', 'zipcode' => '725'], ['name' => '學甲區', 'zipcode' => '726'], ['name' => '北門區', 'zipcode' => '727'], ['name' => '新營區', 'zipcode' => '730'], ['name' => '後壁區', 'zipcode' => '731'], ['name' => '白河區', 'zipcode' => '732'], ['name' => '東山區', 'zipcode' => '733'], ['name' => '六甲區', 'zipcode' => '734'], ['name' => '下營區', 'zipcode' => '735'], ['name' => '柳營區', 'zipcode' => '736'], ['name' => '鹽水區', 'zipcode' => '737'], ['name' => '善化區', 'zipcode' => '741'], ['name' => '大內區', 'zipcode' => '742'], ['name' => '山上區', 'zipcode' => '743'], ['name' => '新市區', 'zipcode' => '744'], ['name' => '安定區', 'zipcode' => '745']]], ['name' => '高雄市', 'code' => 'E', 'telcode' => '07', 'districts' => [['name' => '新興區', 'zipcode' => '800'], ['name' => '前金區', 'zipcode' => '801'], ['name' => '苓雅區', 'zipcode' => '802'], ['name' => '鹽埕區', 'zipcode' => '803'], ['name' => '鼓山區', 'zipcode' => '804'], ['name' => '旗津區', 'zipcode' => '805'], ['name' => '前鎮區', 'zipcode' => '806'], ['name' => '三民區', 'zipcode' => '807'], ['name' => '楠梓區', 'zipcode' => '811'], ['name' => '小港區', 'zipcode' => '812'], ['name' => '左營區', 'zipcode' => '813'], ['name' => '仁武區', 'zipcode' => '814'], ['name' => '大社區', 'zipcode' => '815'], ['name' => '岡山區', 'zipcode' => '820'], ['name' => '路竹區', 'zipcode' => '821'], ['name' => '阿蓮區', 'zipcode' => '822'], ['name' => '田寮區', 'zipcode' => '823'], ['name' => '燕巢區', 'zipcode' => '824'], ['name' => '橋頭區', 'zipcode' => '825'], ['name' => '梓官區', 'zipcode' => '826'], ['name' => '彌陀區', 'zipcode' => '827'], ['name' => '永安區', 'zipcode' => '828'], ['name' => '湖內區', 'zipcode' => '829'], ['name' => '鳳山區', 'zipcode' => '830'], ['name' => '大寮區', 'zipcode' => '831'], ['name' => '林園區', 'zipcode' => '832'], ['name' => '鳥松區', 'zipcode' => '833'], ['name' => '大樹區', 'zipcode' => '840'], ['name' => '旗山區', 'zipcode' => '842'], ['name' => '美濃區', 'zipcode' => '843'], ['name' => '六龜區', 'zipcode' => '844'], ['name' => '內門區', 'zipcode' => '845'], ['name' => '杉林區', 'zipcode' => '846'], ['name' => '甲仙區', 'zipcode' => '847'], ['name' => '桃源區', 'zipcode' => '848'], ['name' => '那瑪夏區', 'zipcode' => '849'], ['name' => '茂林區', 'zipcode' => '851'], ['name' => '茄萣區', 'zipcode' => '852']]], ['name' => '澎湖縣', 'code' => 'X', 'telcode' => '06', 'districts' => [['name' => '馬公市', 'zipcode' => '880'], ['name' => '西嶼鄉', 'zipcode' => '881'], ['name' => '望安鄉', 'zipcode' => '882'], ['name' => '七美鄉', 'zipcode' => '883'], ['name' => '白沙鄉', 'zipcode' => '884'], ['name' => '湖西鄉', 'zipcode' => '885']]], ['name' => '屏東縣', 'code' => 'T', 'telcode' => '08', 'districts' => [['name' => '屏東市', 'zipcode' => '900'], ['name' => '三地門鄉', 'zipcode' => '901'], ['name' => '霧臺鄉', 'zipcode' => '902'], ['name' => '瑪家鄉', 'zipcode' => '903'], ['name' => '九如鄉', 'zipcode' => '904'], ['name' => '里港鄉', 'zipcode' => '905'], ['name' => '高樹鄉', 'zipcode' => '906'], ['name' => '鹽埔鄉', 'zipcode' => '907'], ['name' => '長治鄉', 'zipcode' => '908'], ['name' => '麟洛鄉', 'zipcode' => '909'], ['name' => '竹田鄉', 'zipcode' => '911'], ['name' => '內埔鄉', 'zipcode' => '912'], ['name' => '萬丹鄉', 'zipcode' => '913'], ['name' => '潮州鎮', 'zipcode' => '920'], ['name' => '泰武鄉', 'zipcode' => '921'], ['name' => '來義鄉', 'zipcode' => '922'], ['name' => '萬巒鄉', 'zipcode' => '923'], ['name' => '崁頂鄉', 'zipcode' => '924'], ['name' => '新埤鄉', 'zipcode' => '925'], ['name' => '南州鄉', 'zipcode' => '926'], ['name' => '林邊鄉', 'zipcode' => '927'], ['name' => '東港鎮', 'zipcode' => '928'], ['name' => '琉球鄉', 'zipcode' => '929'], ['name' => '佳冬鄉', 'zipcode' => '931'], ['name' => '新園鄉', 'zipcode' => '932'], ['name' => '枋寮鄉', 'zipcode' => '940'], ['name' => '枋山鄉', 'zipcode' => '941'], ['name' => '春日鄉', 'zipcode' => '942'], ['name' => '獅子鄉', 'zipcode' => '943'], ['name' => '車城鄉', 'zipcode' => '944'], ['name' => '牡丹鄉', 'zipcode' => '945'], ['name' => '恆春鎮', 'zipcode' => '946'], ['name' => '滿州鄉', 'zipcode' => '947']]], ['name' => '臺東縣', 'code' => 'V', 'telcode' => '089', 'districts' => [['name' => '臺東市', 'zipcode' => '950'], ['name' => '綠島鄉', 'zipcode' => '951'], ['name' => '蘭嶼鄉', 'zipcode' => '952'], ['name' => '延平鄉', 'zipcode' => '953'], ['name' => '卑南鄉', 'zipcode' => '954'], ['name' => '鹿野鄉', 'zipcode' => '955'], ['name' => '關山鎮', 'zipcode' => '956'], ['name' => '海端鄉', 'zipcode' => '957'], ['name' => '池上鄉', 'zipcode' => '958'], ['name' => '東河鄉', 'zipcode' => '959'], ['name' => '成功鎮', 'zipcode' => '961'], ['name' => '長濱鄉', 'zipcode' => '962'], ['name' => '太麻里鄉', 'zipcode' => '963'], ['name' => '金峰鄉', 'zipcode' => '964'], ['name' => '大武鄉', 'zipcode' => '965'], ['name' => '達仁鄉', 'zipcode' => '966']]], ['name' => '花蓮縣', 'code' => 'U', 'telcode' => '03', 'districts' => [['name' => '花蓮市', 'zipcode' => '970'], ['name' => '新城鄉', 'zipcode' => '971'], ['name' => '秀林鄉', 'zipcode' => '972'], ['name' => '吉安鄉', 'zipcode' => '973'], ['name' => '壽豐鄉', 'zipcode' => '974'], ['name' => '鳳林鎮', 'zipcode' => '975'], ['name' => '光復鄉', 'zipcode' => '976'], ['name' => '豐濱鄉', 'zipcode' => '977'], ['name' => '瑞穗鄉', 'zipcode' => '978'], ['name' => '萬榮鄉', 'zipcode' => '979'], ['name' => '玉里鎮', 'zipcode' => '981'], ['name' => '卓溪鄉', 'zipcode' => '982'], ['name' => '富里鄉', 'zipcode' => '983']]], ['name' => '金門縣', 'code' => 'W', 'telcode' => '082', 'districts' => [['name' => '金沙鎮', 'zipcode' => '890'], ['name' => '金湖鎮', 'zipcode' => '891'], ['name' => '金寧鄉', 'zipcode' => '892'], ['name' => '金城鎮', 'zipcode' => '893'], ['name' => '烈嶼鄉', 'zipcode' => '894'], ['name' => '烏坵鄉', 'zipcode' => '896']]], ['name' => '連江縣', 'code' => 'Z', 'telcode' => '0836', 'districts' => [['name' => '南   竿', 'zipcode' => '209'], ['name' => '北   竿', 'zipcode' => '210'], ['name' => '莒   光', 'zipcode' => '211'], ['name' => '東   引', 'zipcode' => '212']]],
		];

		foreach ($cities as $key => $city_data) {

			$districts_data = $city_data['districts'];
			$city = City::create([
				'name' => $city_data['name'],
				'code' => $city_data['code'],
				'telcode' => $city_data['telcode'],
			]);

			foreach ($districts_data as $key => $district_data) {
				District::create([
					'name' => $district_data['name'],
					'zipcode' => $district_data['zipcode'],
					'city_id' => $city->id,
				]);
			}

		}
	}
}