<?php

declare(strict_types=1);

namespace phpu\calendar;

use \DateTime;
use \Exception;

/**
 * 日历
 *
 * 该日历的天文算法主要摘自Jean Meeus的《Astronomical Algorithms》所述的计算方法，当然也有部分参考NASA网站或其它网站公布的计算方法。
 * 各计算都有注明计算方法的出处，请自行查看源代码的注释，
 * 如果大家有更精准的计算方法，肯请指教，先谢！！<3
 *
 * 时区
 * 该日历允许使用者自行设定时区，但也有需要注意的地方：
 * 该日历在处理节气、干支、农历日期时使用了中国(东八区)时区，如果您需要在日历中使用节气、干支、农历日期时，需要了解你所在时区与东八区时区的差别，
 * 关于中国时区需要注意的是不同时期的夏令时，中国时间夏令时的调整有很多个时间段，为此，我们在计算节气、干支时我们是固定为+8小时
 * 中国时区夏令时区段有产：1901年前为LMT(29143)，1919-4-12至1919-9-30为CDT(32400)，1940至1942，1945至1949，1986至1991，这些时间段都有夏令时的调整。
 *
 * php支持的适合中国的时区名称有：'Asia/Shanghai','Asia/Chongqing','Asia/Hong_Kong','Asia/Macau','Asia/Taipei','PRC','Etc/GMT-8'...
 * php支持时区名称: https://www.php.net/manual/zh/timezones.php
 *
 * 时间名称简介：
 * 该日历的计算主要用天文历法计算方法，所以我们可以简单了解一下时间名称之间的不同。
 * Universal Time （UT）：世界时，格林威治平太阳时。
 * Temps Atomique International （TAI）：国际原子时，原子时间计量标准在1967年正式取代了天文学的秒长的定义。
 * Coordinated Universal Time （UTC）：协调世界时，Coordinated Universal Time，法文Temps Universel Cordonné，由于英文（CUT）和法文（TUC）的缩写不同，作为妥协，简称 UTC。UTC 基于国际原子时 TAI，添加闰秒保持在 UT1 的 0.9s 内。UTC 是世界时间定义、时钟显示、程序输入的时刻。
 * Greenwich Mean Time （GMT）：格林威治标准时，老的时间计量标准。一般被作为 UTC 的民间名称，可以认为 GMT = UTC 。
 * Terrestrial Time （TT）：力学时，旧时称为地球力学时（Terrestrial Dynamical Time, TDT）。用于星历、日月食、行星动态等，建立在 TAI 基础上。 TT = UTC + 64.184s
 * Barycentric Dynamical Time （TDB）：太阳系质心力学时，有时被简称为质心力学时，一种用以解算坐标原点位于太阳系质心的运动方程（如行星运动方程）并编制其星表时所用的时间系统。
 *
 *
 * class Calendar
 *
 * @package phpu\calendar
 */
class Calendar
{
    // 日历显示

    public const GRID_DAY   = 0; // 显示一天的日历
    public const GRID_WEEK  = 1; // 显示一周的日历
    public const GRID_MONTH = 2; // 显示一个月的日历

    private $timezone;

    private $config = [
        // 读取日历长度 int
        'grid'             => self::GRID_MONTH,

        // 读取节气 bool
        'solar_terms'      => true,

        // 读取干支 bool
        'heavenly_earthly' => true,

        // 读取农历 bool
        'lunar'            => true,

        // 区分早晚子时，true则 23:00-24:00 00:00-01:00为子时，否则00:00-02:00为子时
        'night_zi_hour'    => false,
    ];

    private $lang = [

        // 时区名称,需php支持的时区名称
        'time_zone_name' => 'Asia/Shanghai',

        // 日历显示时第一列显示周几，(日历表第一列是周几,0周日,依次最大值6)
        // int
        'first_day_of_week' => 0,

        // 数字
        'number' => ['日', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十'],

        // 星期
        'weekdays' => ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
        'weekdays_short' => ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
        'weekdays_min' => ['日', '一', '二', '三', '四', '五', '六'],

        // 月
        'months' => ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
        'months_short' => ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],

        // 24节气名词
        'solar_terms' => ['春分', '清明', '谷雨', '立夏', '小满', '芒种', '夏至', '小暑', '大暑', '立秋', '处暑', '白露',
                          '秋分', '寒露', '霜降', '立冬', '小雪', '大雪', '冬至', '小寒', '大寒', '立春', '雨水', '惊蛰'],

        // 10天干
        'heavenly_stems' => ['甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸'],

        // 12地支
        'earthly_branches' => ['子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'],

        // 星座
        'star_sign' => ['水瓶', '双鱼', '白羊', '金牛', '双子', '巨蟹', '狮子', '处女', '天秤', '天蝎', '射手', '摩羯'],

        // 生肖
        'symbolic_animals' => ['鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪'],

        // 农历闰年表示
        'lunar_leap' => '(闰)',

        // 农历相关数字
        'lunar_number' => ['日', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十'],

        // 农历整十表示
        'lunar_whole_tens' => ['初', '十', '廿', '卅'],

        // 农历月份表示
        'lunar_months' => ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],

    ];

    /**
     * 构造
     *
     * Calendar constructor.
     *
     * @param string|null      $configName
     * @param string      $langName
     */
    public function __construct(?string $configName = 'default', string $langName = 'zh-cn')
    {
        // 语言
        $lang = $this->loadLang($langName);

        if (isset($lang['time_zone_name'])){
            $this->setTimeZone($lang['time_zone_name']);
        }

        // 获取配置
        $configName = !$configName ? 'default' : $configName;
        $this->loadConfig($configName);
    }

    /**
     * 对象拷贝
     */
    public function __clone(){
        $this->timezone = clone $this->timezone;
    }

    /**
     * 设置配置
     *
     * @param string $configName
     * @return $this
     */
    public function setConfig(string $configName = 'default')
    {
        $configName = !$configName ? 'default' : $configName;

        $this->loadConfig($configName);

        return $this;
    }

    /**
     * 设置语言
     *
     * @param string $langName
     *
     * @return $this
     */
    public function setLang(string $langName = 'zh-cn'){

        $lang = $this->loadLang($langName);

        if (isset($lang['time_zone_name'])){
            $this->setTimeZone($lang['time_zone_name']);
        }

        return $this;
    }

    /**
     * 设置timezone
     *
     * @param string $timeZoneName php支持的时间名称
     *
     * @return $this
     */
    public function setTimeZone(string $timeZoneName = 'Asia/Shanghai')
    {

        try {
            $DateTimeZone = new \DateTimeZone(trim($timeZoneName));
        }catch(\Exception $e) {
            $timeZoneName = 'Asia/Shanghai';
            $DateTimeZone = new \DateTimeZone($timeZoneName);
        }
        $this->timezone = $DateTimeZone;

        return $this;
    }

    /**
     * 返回时区名称
     *
     * @return string
     */
    public function getTimeZoneName():string
    {
        return $this->timezone->getName();
    }


    /**
     * 生成一个日历
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hours
     *
     * @return array
     * @throws Exception
     */
    public function createCalendar(int $year, int $month, int $day = 0, int $hours=-1):array
    {
        if($year < -1000 || $year > 3000){
            throw new \DomainException('年份限-1000年至3000年');
        }

        // 当前日期时间
        $currentDt = $this->now();
        [$currentY,$currentM,$currentD,$currentH,$currentI,$currentS,$currentW]
            = array_map(function($v){return intval($v,10);}, explode(',',$currentDt->format('Y,n,j,G,i,s,w')));
        if($day === 0){
            // 未指定日，默认为1日
            $day = 1;
            // 如果年月都与当前年月相同，而日又未指定，则使用当前的日。
            if($month === $currentM && $year === $currentY){
                $day = $currentD;
            }
        }

        // 未指定小时，默认为当前时间的小时数。
        if($hours < 0){
            $hours = $currentH;
        }

        $newDt = $this->newDateTime($year, $month, $day, $hours, $currentI, $currentS);
        [$year,$month,$day,$hours,$minutes,$seconds,$week]
            = array_map(function($v){return intval($v,10);}, explode(',',$newDt->format('Y,n,j,G,i,s,w')));

        // 取出一整年的节气
        $yearSolarTerms = [];
        if($this->config['solar_terms']){
            $yearSolarTerms = $this->yearSolarTerms($newDt);
        }

        $thisDayKey = $year.'-'.$month.'-'.$day;

        // 节气
        $solar_terms = [];
        if($yearSolarTerms && isset($yearSolarTerms[$thisDayKey])){
            $solar_terms = $yearSolarTerms[$thisDayKey];
        }

        // 干支
        $gz = [];
        if($this->config['heavenly_earthly']){
            $gz = $this->sexagenaryCycle($newDt);
        }

        // 农历
        $lunar = [];
        if($this->config['lunar']){
            $lunar = $this->lunarDate($newDt);
        }

        return [
            'y'           => $year,
            'm'           => $month,
            'd'           => $day,
            'h'           => $hours,
            'wi'          => $week,
            'w'           => $this->lang['weekdays_min'][$week],
            'solar_terms' => $solar_terms,
            'gz'          => $gz,
            'lunar'       => $lunar,
            'days'        => $this->days($newDt, $yearSolarTerms)
        ];

    }

    /**
     * 以字符串新建一个DateTime
     *
     * @param string $timeStr 字符串格式与DateTime构造时所需相同，参考 https://php.net/manual/en/datetime.formats.php
     *
     * @return DateTime
     * @throws Exception
     */
    private function stringToDateTime(string $timeStr):DateTime
    {
        return new DateTime($timeStr, $this->timezone);
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     *
     * @return DateTime
     * @throws Exception
     */
    private function newDateTime(int $year, int $month, int $day = 0, int $hours = 0, $minutes = 0, $seconds = 0):DateTime
    {
        return $this->now()->setDate($year, $month, $day)->setTime($hours,$minutes,$seconds);
    }

    /**
     * 当前时间的 DateTime 对象
     *
     * @return DateTime
     * @throws Exception
     */
    private function now():DateTime
    {
        return $this->stringToDateTime('now');
    }

    /**
     * 取日历内的所有天数的数据
     * [
     *     [string年-月-日, [
     *         'gregorian' => [y:int年, m:int月, d:int日, h:int小时, wi:int周索引, w:string周名称],
     *         'solar_terms' => [
     *             string年-月-日 => [string节气名称, string节气时分秒],
     *             ...
     *             ],
     *         'gz' => [y:string年干支, m:string月干支, d:string日干支, h:string时干支],
     *         'lunar' => [
     *             [string年份数字, string(闰)月名称, string日名称],
     *             ...
     *             ],
     *         ]
     *     ]
     * ]
     *
     * @param DateTime $dateTime
     * @param array $yearSolarTerms = []
     *
     * @return array
     */
    public function days(DateTime $dateTime,array $yearSolarTerms = []):array
    {
        // 日历显的每周从周几开始,允许[0,1,2,3,4,5,6] 0表示周日,6表示周六
        $first_day_of_week = intval($this->lang['first_day_of_week'], 10);
        if($first_day_of_week < 0 || $first_day_of_week > 6){
            $first_day_of_week = 0;
        }

        $startDt = clone $dateTime;

        $recurrences = 0; // 在开始日期基础上重复几次+1日，以取日期用于日历显示

        $dateRange = [$startDt]; // 转iterable类型,这里转为一个数组

        if($this->config['grid'] === self::GRID_WEEK || $this->config['grid'] === self::GRID_MONTH){

            $recurrences = 6; // 6表示取7个日期

            if($this->config['grid'] === self::GRID_MONTH){
                $recurrences = 41; // 41表示取42个日期
                $startDt->modify('first day of this month'); // $startDt为当月首日(1日)
            }

            $w = (int) $startDt->format('w');

            $subdays = 0;
            if($w >= $first_day_of_week){
                $subdays = $w - $first_day_of_week;
            }else{
                $subdays = 7 - $first_day_of_week + $w;
            }
            if ($subdays > 0) {
                $startDt->sub(\DateInterval::createFromDateString($subdays.' day'));
            }

            $dateRange = new \DatePeriod(
                $startDt, // 开始日期
                new \DateInterval('P1D'), // 递增1天
                (int) $recurrences
            );

        }

        $resultDate = [];
        foreach($dateRange as $date){
            $resultDate[] = $this->day($date, $yearSolarTerms);
        }

        return $resultDate;

    }

    /**
     * 取一天的数据
     *
     * @param DateTime $dateTime
     * @param array    $yearSolarTerms 当年的节气
     *
     * @return array
     */
    public function day(DateTime $dateTime, $yearSolarTerms = []):array
    {
        $day = [];

        [$y,$m,$d,$h,$i,$s,$w]
            = array_map(function($v){return intval($v,10);}, explode(',', $dateTime->format('Y,n,j,G,i,s,w')));

        $key = $y . '-' . $m . '-' . $d;
        $day['gregorian'] = [
            'y' => (int)$y,
            'm' => (int)$m,
            'd' => (int)$d,
            'h' => (int)$h,
            'wi' => (int)$w,
            'w' => $this->lang['weekdays_min'][$w]
        ];

        // 节气
        $day['solar_terms'] = [];
        if($yearSolarTerms && isset($yearSolarTerms[$key])){
            $day['solar_terms'][$key] = $yearSolarTerms[$key];
        }

        // 干支
        $day['gz'] = [];
        if($this->config['heavenly_earthly']){
            $day['gz'] = $this->sexagenaryCycle($dateTime);
        }

        // 农历
        $day['lunar'] = [];
        if($this->config['lunar']){
            $day['lunar'] = $this->lunarDate($dateTime);
        }

        return [$key,$day];
    }

    /**
     * 由DateTime生成当月首日的另一个DateTime
     *
     * @param DateTime $dateTime
     *
     * @return DateTime
     */
    private static function firstDayMonth(DateTime $dateTime):DateTime
    {
        $dt = clone $dateTime;

        return $dt->modify('first day of this month');
    }


    /**
     * 取节气
     * 如果异常，将返回空数组
     *
     * @param DateTime $dateTime
     *
     * @return array map[string]array  [string‘年-月-日’=>[string节气名称,string节气时间(时:分:秒)], ...]
     */
    public function yearSolarTerms(DateTime $dateTime):array
    {
        $year = intval($dateTime->format('Y'), 10);
        $timeZoneName = ($dateTime->getTimezone())->getName();

        try {
            $st = SolarTerm::solarTerms($year,$timeZoneName);
        } catch (\Exception $e) {
            return [];
        }

        $lang_solar_terms = ($this->lang)['solar_terms'];
        $sts = [];
        foreach ($st as $stk=>$stv){
            $sts[$stk] = [
                $lang_solar_terms[$stv['i']],
                $stv['d']->format('H:i:s')
            ];
        }

        return $sts;
    }

    /**
     * 取干支
     * 依公历日期时间取干支,
     * 生肖依地支索引
     *
     * @param DateTime $dateTime
     *
     * @return string[] ['y'=>['s'=>string年干支名称,‘sa’=>string年生肖,'g'=>int年干索引,'z'=>int年支索引], ['m'=>['s','sa','g','z']], ['d'=>['s','sa','g','z']], ['h'=>['s','sa','g','z']]]
     */
    public function sexagenaryCycle(DateTime $dateTime):array
    {

        [$year,$month,$day,$hours,$minutes,$seconds]
            = array_map(function($v){return intval($v,10);}, explode(',',$dateTime->format('Y,n,j,G,i,s')));

        $scs = ChineseCalendar::sexagenaryCycle($year,$month,$day,$hours,$minutes,$seconds, $this->config['night_zi_hour']);

        $lang_hs_strings = ($this->lang)['heavenly_stems'];   // 天干
        $lang_eb_strings = ($this->lang)['earthly_branches']; // 地支
        $lang_sa_strings = ($this->lang)['symbolic_animals']; // 生肖


        return [
            'y' => ['s'=>$lang_hs_strings[$scs['y']['g']].$lang_eb_strings[$scs['y']['z']], 'sa'=>$lang_sa_strings[$scs['y']['z']], 'g'=>$scs['y']['g'], 'z'=>$scs['y']['z']],
            'm' => ['s'=>$lang_hs_strings[$scs['m']['g']].$lang_eb_strings[$scs['m']['z']], 'sa'=>$lang_sa_strings[$scs['m']['z']], 'g'=>$scs['m']['g'], 'z'=>$scs['m']['z']],
            'd' => ['s'=>$lang_hs_strings[$scs['d']['g']].$lang_eb_strings[$scs['d']['z']], 'sa'=>$lang_sa_strings[$scs['d']['z']], 'g'=>$scs['d']['g'], 'z'=>$scs['d']['z']],
            'h' => ['s'=>$lang_hs_strings[$scs['h']['g']].$lang_eb_strings[$scs['h']['z']], 'sa'=>$lang_sa_strings[$scs['h']['z']], 'g'=>$scs['h']['g'], 'z'=>$scs['h']['z']],
        ];
    }

    /**
     * 取出农历日期
     *
     * @param DateTime $dateTime
     *
     * @return string[] [年份数,月中文,日中文]
     */
    public function lunarDate(DateTime $dateTime):array
    {
        [$year,$month,$day]
            = array_map(function($v){return intval($v,10);}, explode(',',$dateTime->format('Y,n,j')));

        $lunarDate = ChineseCalendar::gregorianToLunar($year,$month,$day);

        return [
            (string)$lunarDate['Y'],
            $this->lunarMonthChinese($lunarDate['n'],$lunarDate['leap']),
            $this->lunarDayChinese($lunarDate['j'])
        ];
    }

    /**
     * 取星座
     *
     * @param DateTime $dateTime
     *
     * @return array [int星座索引, string星座名称]
     */
    public function sign(DateTime $dateTime):array
    {
        // 星座只要知道月和日就行了
        [$month,$day] = array_map(function($v){return intval($v,10);}, explode(',',$dateTime->format('n,j'),2));

        $i = self::signIndex($month,$day);

        return [$i, ($this->lang['star_sign'])[$i]];
    }

    /**
     * 星座索引
     *
     * @param int $month 月
     * @param int $day   日
     *
     * @return int 星座索引值
     */
    private static function signIndex(int $month, int $day):int
    {
        $dds = [20,19,21,20,21,22,23,23,23,24,22,22]; //星座的起始日期

        $kn = $month - 1; //下标从0开始

        if ($day < $dds[$kn]){ //如果早于该星座起始日期,则往前一个
            $kn = (($kn + 12) - 1) % 12; //确保是正数
        }

        return (int)$kn;
    }

    /**
     * 农历月份数转中文表示
     *
     * @param int $month  农历月份数
     * @param int $isLeap 是否闰月
     *
     * @return string
     */
    private function lunarMonthChinese(int $month, int $isLeap = 0):string
    {
        if($month < 1 || $month > 12){
            return '';
        }

        $leapstr = $isLeap ? $this->lang['lunar_leap'] : '';

        return $leapstr . ($this->lang['lunar_months'])[$month - 1];
    }

    /**
     * 农历日数字转中文表示
     *
     * @param int $day 农历的日数
     *
     * @return string 中文表示法 如：初五，初十，二十，廿五
     */
    private function lunarDayChinese(int $day):string
    {
        // 农历每月的天数不能超过30
        if ($day < 1 || $day > 30){
            return '';
        }

        $daystr = '';

        switch ($day){
            case 10 : $daystr = ($this->lang['lunar_whole_tens'])[0] . ($this->lang['lunar_number'])[10]; // 初十
                break;
            case 20 : $daystr = ($this->lang['lunar_number'])[2] . ($this->lang['lunar_number'])[10];     // 二十
                break;
            case 30 : $daystr = ($this->lang['lunar_number'])[3] . ($this->lang['lunar_number'])[10];     // 三十
                break;
            default:
                $k = $day / 10;
                $m = $day % 10;
                $daystr = ($this->lang['lunar_whole_tens'])[$k] . ($this->lang['lunar_number'])[$m];
        }

        return $daystr;
    }

    /**
     * 引入配置
     *
     * @param string $name
     *
     * @return array
     */
    private function loadConfig(string $name = 'default'):array
    {
        $configFile = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
        if (is_file($configFile)) {
            $cfg = include $configFile;
            if (is_array($cfg) && isset($cfg['default'])){
                $this->config = $cfg['default'];
                if ($name !== 'default' && isset($cfg[$name])){
                    $this->config = array_merge($this->config, array_change_key_case($cfg[$name]));
                }
            }
        }

        return $this->config;
    }

    /**
     * 引及语言文件
     *
     * @param string $langName
     *
     * @return array
     */
    private function loadLang(string $langName = 'zh-cn'):array
    {
        $langFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'locales';
        $langFile = $langFilePath . DIRECTORY_SEPARATOR . $langName . '.php';

        if (!is_dir($langFilePath)){
            return $this->lang;
        }

        if(!is_file($langFile)){
            return $this->lang;
        }

        $lang = include $langFile;

        if(!is_array($lang)){
            return $this->lang;
        }

        $this->lang = array_merge($this->lang, array_change_key_case($lang));

        return $this->lang;
    }



}

