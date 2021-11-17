<?php

declare(strict_types=1);

namespace phpu\calendar;


use \DateTime;

/**
 * 中国历法相关
 * 现实现：农历,干支
 * 注意: 农历公历互换皆以中国(东八区)时间为基础计算
 *
 * Class ChineseCalendar
 *
 * @package phpu\calendar
 */
class ChineseCalendar
{
    /**
     * 中国(东八区)时间相对UTC的偏移量(单位：天days)
     *
     * @var float
     */
    public const CHINESE_TIME_OFFSET = 8/24.0;

    /**
     * 以某年立春点开始的节
     *
     * @var array [int年份数 => array[16]float]
     */
    private static $jsses = [];


    /**
     * 以前一年冬至为起点之连续16个中气
     *
     * @var array  [int年份数 => array[16]float]
     */
    private static $zqs = [];


    /**
     * 农历转公历
     *
     * @param int    $year         农历年
     * @param int    $month        农历月
     * @param int    $day          农历日
     * @param int    $isLeap       指定的月是否是闰月
     * @param string $timeZoneName 时区名称 默认: 'Asia/Shanghai' 由于农历对应的是中国时间，故此在将农历转为公历时建议转为中国时间
     *
     * @return DateTime
     * @throws \Exception
     */
    public static function lunarToGregorian(int $year, int $month, int $day, int $isLeap=0, string $timeZoneName = 'Asia/Shanghai'): DateTime
    {
        if ($year < -1000 || $year > 3000) { // 适用于公元-1000至公元3000,超出此范围误差较大
            throw new \DomainException('年份限-1000年至3000年');
        }
        if ($month < 1 || $month > 12){ // 月份须在1-12月之内
            throw new \DomainException('月份错误');
        }
        if ($day < 1 || $day > 30) { //输入日期必须在1-30日之內
            throw new \DomainException('日期错误');
        }

        [, $nm, $mc] = self::zQandSMandLunarMonthCode($year);

        $leap = self::mcLeap($mc);

        // 11月对应到1,12月对应到2,1月对应到3,2月对应到4,依此类推
        $month += 2;

        $nofd = [];
        // 求算农历各月之大小,大月30天,小月29天
        for ($i = 0; $i <= 14; $i++) {
            $nofd[$i] = floor($nm[$i + 1] + 0.5) - floor($nm[$i] + 0.5); // 每月天数,加0.5是因JD以正午起算
        }

        $jd = 0; // 儒略日时间
        $errMsg = null;

        if ($isLeap){ // 闰月
            if ($leap < 3) { // 而旗标非闰月或非本年闰月,则表示此年不含闰月.leap=0代表无闰月,=1代表闰月为前一年的11月,=2代表闰月为前一年的12月
                $errMsg = '闰月非该年，是上一年的闰月';
            } else { // 若该年內有闰月
                if ($leap != $month) { // 但不为指定的月份
                    $errMsg = '该月非该年的闰月'; // 该月非该年的闰月
                } else { // 若指定的月份即为闰月
                    if ($day <= $nofd[$month]) { // 若日期不大于当月天数
                        $jd = $nm[$month] + $day - 1; // 则将当月之前的JD值加上日期之前的天数
                    } else { // 日期超出范围
                        $errMsg = '日期超出范围';
                    }
                }
            }
        } else { // 若没有指明是闰月
            if ($leap == 0) { // 若旗标非闰月,则表示此年不含闰月(包括前一年的11月起之月份)
                if ($day <= $nofd[$month - 1]) { // 若日期不大于当月天数
                    $jd = $nm[$month - 1] + $day - 1; // 则将当月之前的JD值加上日期之前的天数
                } else { // 日期超出范围
                    $errMsg = '日期超出范围';
                }
            } else { // 若旗标为本年有闰月(包括前一年的11月起之月份) 公式nofd(mx - (mx > leap) - 1)的用意为:若指定月大于闰月,则索引用mx,否则索引用mx-1
                if ($day <= $nofd[$month + ($month > $leap) - 1]) { // 若日期不大于当月天数
                    $jd = $nm[$month + ($month > $leap) - 1] + $day - 1; // 则将当月之前的JD值加上日期之前的天数
                } else { // 日期超出范围
                    $errMsg = '日期超出范围';
                }
            }
        }

        // 去掉时分秒
        $jd = $jd - self::CHINESE_TIME_OFFSET; // 还原经过中国(东八区)时差处理的jd

        // 由于农历对应的是中国时间，故此在将农历转为公历时建议转为中国时间
        if(is_null($errMsg)){
            return Julian::jdToDateTime($jd,$timeZoneName)->setTime(0,0,0,0);
            //return Julian::jdToDateTime($jd,$timeZoneName);
        }else{
            throw new \InvalidArgumentException($errMsg);
        }
    }

    /**
     * 公历日期转农历日期
     * 中国(东八区)时区日期转农历日期
     *
     * @param int $year  公历年份
     * @param int $month 公历月份
     * @param int $day   公历日
     *
     * @return int[] map[string]int  [Y:int年,n:int月,j:int日,leap:int是否闰月(0不是闰月,1是闰月)]
     */
    public static function gregorianToLunar(int $year,int $month,int $day):array
    {
        if ($year < -1000 || $year > 3000) { // 适用于公元-1000至公元3000,超出此范围误差较大
            throw new \DomainException('年份限-1000年至3000年');
        }
        if ($month < 1 || $month > 12){ // 月份须在1-12月之内
            throw new \DomainException('月份错误');
        }
        if ($day < 1 || $day > 31) { //输入日期必须在1-30日之內
            throw new \DomainException('日期错误');
        }

        $yy = $year; // 初始农历年等于公历年

        $prev = 0; // 是否跨年了,跨年了则减一
        $isLeap = 0;// 是否闰月

        [, $nm, $mc] = self::zQandSMandLunarMonthCode($year);

        $jd = Julian::julianDay($year, $month, $day, 0, 0, 0, 0); // 求出指定年月日之JD值
        $jdn = $jd + 0.5; // 加0.5是将起始点从正午改为0时开始

        // 如果公历日期的jd小于第一个朔望月新月点，表示农历年份是在公历年份的上一年
        if (floor($jdn) < floor($nm[0] + 0.5)) {
            $prev = 1;
            [, $nm, $mc] = self::zQandSMandLunarMonthCode($year-1);
        }

        // 查询对应的农历月份索引
        $mi = 0;
        for ($i = 0; $i <= 14; $i++) { // 指令中加0.5是为了改为从0时算起而不是从中午算起
            if (floor($jdn) >= floor($nm[$i] + 0.5) && floor($jdn) < floor($nm[$i + 1] + 0.5)) {
                $mi = $i;
                break;
            }
        }

        // 农历的年
        // 如果月份属于上一年的11月或12月,或者农历年在上一年时
        if ($mc[$mi] < 2 || $prev == 1) {
            $yy -= 1;
        }

        // 农历月份是否是闰月
        if (($mc[$mi] - floor($mc[$mi])) * 2 + 1 != 1) { // 因mc(mi)=0对应到前一年农历11月,mc(mi)=1对应到前一年农历12月,mc(mi)=2对应到本年1月,依此类推
            $isLeap = 1;
        }
        // 农历的月
        $mm = floor($mc[$mi] + 10) % 12 + 1;

        // 农历的日
        $dd = floor($jdn) - floor($nm[$mi] + 0.5) + 1; // 日,此处加1是因为每月初一从1开始而非从0开始

        return ['Y'=>(int)$yy,'n'=>(int)$mm,'j'=>(int)$dd,'leap'=>$isLeap];
    }

    /**
     * 农历某个月有多少天
     *
     * @param int $year   农历年数字
     * @param int $month  农历月数字
     * @param int $isLeap 是否是闰月
     *
     * @return int 农历某个月天数
     */
    public static function lunarDays(int $year, int $month, int $isLeap=0):int
    {
        if ($year < -1000 || $year > 3000) { // 适用于公元-1000至公元3000,超出此范围误差较大
            throw new \DomainException('年份限-1000年至3000年');
        }
        if ($month < 1 || $month > 12){ // 月份须在1-12月之内
            throw new \DomainException('月份错误');
        }

        [, $nm, $mc] = self::zQandSMandLunarMonthCode($year);

        $leap = self::mcLeap($mc); // 闰几月，0无闰月

        // 11月对应到1,12月对应到2,1月对应到3,2月对应到4,依此类推
        $month += 2;

        $nofd = [];
        // 求算农历各月之大小,大月30天,小月29天
        for ($i = 0; $i <= 14; $i++) {
            $nofd[$i] = floor($nm[$i + 1] + 0.5) - floor($nm[$i] + 0.5); // 每月天数,加0.5是因JD以正午起算
        }

        $dy = 0; // 当月天数

        if ($isLeap){ // 闰月
            if ($leap < 3) { // 而旗标非闰月或非本年闰月,则表示此年不含闰月.leap=0代表无闰月,=1代表闰月为前一年的11月,=2代表闰月为前一年的12月
                throw new \InvalidArgumentException('该年无闰月'); // 该年不是闰年
            } else { // 若本年內有闰月
                if ($leap != $month) { // 但不为指定的月份
                    throw new \InvalidArgumentException('闰月月份不正确'); // 该月非该年的闰月，此月不是闰月
                } else { // 若指定的月份即为闰月
                    $dy = $nofd[$month];
                }
            }
        } else { // 若没有指明是闰月
            if ($leap == 0) { // 若旗标非闰月,则表示此年不含闰月(包括前一年的11月起之月份)
                $dy = $nofd[$month - 1];
            } else { // 若旗标为本年有闰月(包括前一年的11月起之月份) 公式nofd(mx - (mx > leap) - 1)的用意为:若指定月大于闰月,则索引用mx,否则索引用mx-1
                $dy = $nofd[$month + ($month > $leap) - 1];
            }
        }

        return (int)$dy;
    }

    /**
     * 获取农历某年的闰月,0为无闰月
     *
     * @param int       $year       农历年
     *
     * @return int 闰几月，返回值是几就闰几月，0为无闰月
     */
    public static function leap(int $year):int
    {
        [, , $mc] = self::zQandSMandLunarMonthCode($year);

        $leap = self::mcLeap($mc);

        return (int)max(0, $leap-2);
    }

    /**
     * 以比较日期法求算冬月及其余各月名称代码,包含闰月,冬月为0,腊月为1,正月为2,其余类推.闰月多加0.5
     *
     * @param int $year 指定的年
     *
     * @return array   array[3]array [float 以前一年冬至为起点之连续15个中气, float 以含冬至中气为阴历11月(冬月)开始的连续16个朔望月的新月点, float 月名称代码]
     */
    private static function zQandSMandLunarMonthCode(int $year):array
    {
        $mc = [];

        // 取得以前一年冬至为起点之连续16个中气
        $zq = self::qiSinceWinterSolstice($year);

        $nm = self::sMsinceWinterSolstice($year, $zq[0]); // 求出以含冬至中气为阴历11月(冬月)开始的连续16个朔望月的新月点

        $yz = 0; // 设定旗标,0表示未遇到闰月,1表示已遇到闰月
        if (floor($zq[12] + 0.5) >= floor($nm[13] + 0.5)) { // 若第13个中气zq(12)大于或等于第14个新月nm(13)
            for ($i = 1; $i <= 14; $i++) { // 表示此两个冬至之间的11个中气要放到12个朔望月中,
                // 至少有一个朔望月不含中气,第一个不含中气的月即为闰月
                // 若阴历腊月起始日大於冬至中气日,且阴历正月起始日小于或等于大寒中气日,则此月为闰月,其余同理
                if (floor($nm[$i] + 0.5) > floor($zq[$i - 1 - $yz] + 0.5)
                    && floor($nm[$i + 1] + 0.5) <= floor($zq[$i - $yz] + 0.5)) {
                    $mc[$i] = floatval($i - 0.5);
                    $yz = 1; // 标示遇到闰月
                } else {
                    $mc[$i] = floatval($i - $yz); // 遇到闰月开始,每个月号要减1
                }
            }

        } else { // 否则表示两个连续冬至之间只有11个整月,故无闰月
            for ($i = 0; $i <= 12; $i++) { // 直接赋予这12个月月代码
                $mc[$i] = (float)$i;
            }
            for ($i = 13; $i <= 14; $i++) { // 处理次一置月年的11月与12月,亦有可能含闰月
                // 若次一阴历腊月起始日大于附近的冬至中气日,且农历正月起始日小于或等于大寒中气日,则此月为腊月,次一正月同理.
                if (($nm[$i] + 0.5) > floor($zq[$i - 1 - $yz] + 0.5)
                    && floor($nm[$i + 1] + 0.5) <= floor($zq[$i - $yz] + 0.5)) {
                    $mc[$i] = floatval($i - 0.5);
                    $yz = 1; // 标示遇到闰月
                } else {
                    $mc[$i] = floatval($i - $yz); // 遇到闰月开始,每个月号要减1
                }
            }
        }

        return [$zq, $nm, $mc];
    }

    /**
     * 求算以含冬至中气为阴历11月开始的连续16个朔望月
     *
     * @param int $year 指定的年份数
     * @param float $dzjd 上一年的冬至jd
     * @return array
     */
    private static function sMsinceWinterSolstice(int $year, float $dzjd):array
    {
        $tjd = [];

        $novemberJd = Julian::julianDay($year-1, 11, 1, 0); // 求年初前两个月附近的新月点(即前一年的11月初)

        $kn = Astronomy::referenceLunarMonthNum($novemberJd); // 求得自2000年1月起第kn个平均朔望日及期JD值

        for ($i = 0; $i <= 19; $i++) { // 求出连续20个朔望月
            $k = $kn + $i;

            $tjd[$i] = Astronomy::trueNewMoon($k) + self::CHINESE_TIME_OFFSET; // 以k值代入求瞬时朔望日,农历计算需要，加上中国(东八区)时差

            // 修正dynamical time to Universal time
            // 1为1月，0为前一年12月，-1为前一年11月(当i=0时，i-1代表前一年11月)
            $tjd[$i] = $tjd[$i] - Astronomy::deltaTDays($year, floatval($i - 1.0));
        }

        $jj = 0;
        for ($j = 0; $j <= 18; $j++) {
            if (floor($tjd[$j] + 0.5) > floor($dzjd + 0.5)) {
                $jj = $j;
                break;
            } // 已超过冬至中气(比较日期法)
        }

        $nm = [];
        for ($k = 0; $k <= 15; $k++) { // 取上一步的索引值
            $nm[$k] = $tjd[$jj - 1 + $k]; // 重排索引,使含冬至朔望月的索引为0
        }

        return $nm;
    }

    /**
     * 从农历的月代码$mc中找出闰月
     * @param array $mc
     * @return int 0无闰月，1表示上一年的11月，2表示上一年的12月，3表示本年正月，4表示本年二月，5表示本年三月...依此类推
     */
    private static function mcLeap(array $mc):int
    {
        $leap = 0; // 若闰月旗标为0代表无闰月
        for ($j = 1; $j <= 14; $j++) { // 确认本年的上一年11月开始各月是否闰月
            if ($mc[$j] - floor($mc[$j]) > 0) { // 若是,则将此闰月代码放入闰月旗标内
                $leap = intval(floor($mc[$j] + 0.5)); // leap = 0对应农历上一年11月,1对应农历上一年12月,2对应农历本年1月,依此类推.
                break;
            }
        }

        return $leap;
    }

    /**
     * 求出以某年立春点开始的节(注意:为了方便计算起运数,此处第0位为上一年的小寒)
     *
     * @param int $year
     *
     * @return array array[16]float  [儒略日...]
     */
    private static function pureJieSinceSpring(int $year):array
    {
        if(isset(self::$jsses[$year])){
            return self::$jsses[$year];
        }

        $jss = [];

        $lastYearAsts = SolarTerm::lastYearSolarTerms($year);
        for ($i=19;$i<=23;$i++){
            if($i%2 == 0)continue;
            $jss[] = $lastYearAsts[$i] + self::CHINESE_TIME_OFFSET; // 农历计算需要，加上中国(东八区)时差
        }

        // $jdpjq[0] = $lastYearAsts[19] + self::CHINESE_TIME_OFFSET; // 19小寒
        // $jdpjq[1] = $lastYearAsts[21] + self::CHINESE_TIME_OFFSET; // 21立春
        // $jdpjq[2] = $lastYearAsts[23] + self::CHINESE_TIME_OFFSET; // 23惊蛰

        $asts = SolarTerm::adjustedSolarTerms($year, 0, 25); // 求出指定年节气之JD值,从春分开始,到大寒,多取两个确保覆盖一个公历年,也方便计算起运数

        foreach ($asts as $k => $v){
            if($k % 2 == 0){
                continue;
            }
            $jss[] = $asts[$k] + self::CHINESE_TIME_OFFSET; // 农历计算需要，加上中国(东八区)时差
        }

        self::$jsses[$year] = $jss;

        return $jss;
    }

    /**
     * 求出自冬至点为起点的连续16个中气
     *
     * @param int $year
     *
     * @return array array[15]float  [儒略日...]
     */
    private static function qiSinceWinterSolstice(int $year):array
    {
        if(isset(self::$zqs[$year])){
            return self::$zqs[$year];
        }

        $zq = [];

        $lastYearAsts = SolarTerm::lastYearSolarTerms($year);

        for ($i=18;$i<=22;$i++){
            if($i%2 != 0)continue;
            $zq[] = $lastYearAsts[$i] + self::CHINESE_TIME_OFFSET; // 农历计算需要，加上中国(东八区)时差
        }
        // $zq[0] = $lastYearAsts[18] + self::CHINESE_TIME_OFFSET; // 冬至(上一年)
        // $zq[1] = $lastYearAsts[20] + self::CHINESE_TIME_OFFSET; // 大寒
        // $zq[2] = $lastYearAsts[22] + self::CHINESE_TIME_OFFSET; // 雨水

        $asts = SolarTerm::adjustedSolarTerms($year, 0, 25); // 求出指定年节气之JD值

        foreach ($asts as $k => $v){
            if($k%2 != 0){
                continue;
            }
            $zq[] = $asts[$k] + self::CHINESE_TIME_OFFSET; // 农历计算需要，加上中国(东八区)时差
        }

        self::$zqs[$year] = $zq;

        return $zq;
    }

    /**
     * 干支四柱
     * 以立春开始计算，月按节计算
     * 干支推算使用的是公历日期,对应的是中国(东八区)时区的日期时间
     * 为了日历使用，立春及节使用当日计算，干支年月日不再精确到时分秒
     *
     * @param int $year 公历年份
     * @param int $month 公历月份
     * @param int $day 公历日份
     * @param int $hours 小时
     * @param int $minutes 分
     * @param int $seconds 秒
     * @param bool $nightZiHour 是否区分 早晚子时, true则 23:00-24:00 00:00-01:00为子时，否则00:00-02:00为子时
     *
     * @return array
     */
    public static function sexagenaryCycle(int $year, int $month = 1, int $day = 1, int $hours = 12, int $minutes = 0, int $seconds = 1, bool $nightZiHour = false):array
    {
        $jd = Julian::julianDay($year,$month,$day,$hours,$minutes,max(1, $seconds));

        $gz = [];

        // 取得自立春开始的节(不包含中气)，该数组长度固定为16
        $jss = self::pureJieSinceSpring($year);

        // 以立春当天0时作比较，不考虑定立春的时分秒，所以用floor取整数部分
        if (floor($jd + 0.5) < floor($jss[1] + 0.5)) { // $jss[1]为立春，约在2月5日前后。
            $year -= 1; // 若小于$jss[1]则属于前一个节气年

            // 取得自立春开始的节(不包含中气)，该数组长度固定为16
            $jss = self::pureJieSinceSpring($year);
        }

        $ygz = (($year + 4712 + 24) % 60 + 60) % 60;
        $gz['y']['g'] = $ygz % 10; //年干
        $gz['y']['z'] = $ygz % 12; //年支

        $ix = 0;
        for ($j = 0; $j <= 15; $j++) { // 比较求算节气月，求出月干支
            if (floor($jss[$j] + 0.5) > floor($jd + 0.5)) { // 已超过指定时刻，故应取前一个节气，用jd的0时比较，不考虑时分秒
                $ix = $j-1;
                break;
            }
        }

        $tmm = (($year + 4712) * 12 + ($ix - 1) + 60) % 60; // 数组0为前一年的小寒所以这里再减一
        $mgz = ($tmm + 50) % 60;
        $gz['m']['g'] = $mgz % 10; // 月干
        $gz['m']['z'] = $mgz % 12; // 月支

        $jdn = $jd + 0.5; // 计算日柱的干支，加0.5是将起始点从正午改为0时开始
        $thes = (($jdn - floor($jdn)) * 86400) + 3600; // 将jd的小数部分化为秒，并加上起始点前移的一小时(3600秒)
        $dayjd = floor($jdn) + $thes / 86400; // 将秒数化为日数，加回到jd的整数部分
        $dgz = (floor($dayjd + 49) % 60 + 60) % 60;
        $gz['d']['g'] = $dgz % 10; // 日干
        $gz['d']['z'] = $dgz % 12; // 日支
        if($nightZiHour && ($hours >= 23)){ // 区分早晚子时,日柱前移一柱
            $gz['d']['g'] = ($gz['d']['g'] + 10 - 1) % 10;
            $gz['d']['z'] = ($gz['d']['z'] + 12 - 1) % 12;
        }

        $dh = ($dayjd) * 12; // 计算时柱的干支
        $hgz = (floor($dh + 48) % 60 + 60) % 60;
        $gz['h']['g'] = $hgz % 10; // 时干
        $gz['h']['z'] = $hgz % 12; // 时支

        return $gz;
    }



}