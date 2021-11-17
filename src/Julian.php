<?php
/**
 * 儒略历相关计算
 * 该类下所用时间没有特别注明时，是指TT时间
 * 不同场合引用该类时，根据精确度需要自行转换，
 */

declare(strict_types=1);

namespace phpu\calendar;

use \DateTime;
use \Exception;

/**
 * 儒略日相关
 *
 * class Julian
 *
 * @package phpu\calendar
 */
class Julian
{
    /**
     * 格里历TT时间1582年10月15日中午12点的儒略日
     *
     * @var float
     */
    public const JULIAN_GREGORIAN_BOUNDARY = 2299161.0;

    /**
     * J2000.0的儒略日
     * TT时间2000年1月1日中午12点 (UTC时间2000年1月1日11:58:55.816)的儒略日
     *
     *
     * @var float
     */
    public const JULIAN_DAY_J2000 = 2451545.0;


    /**
     * 儒略历1年有多少天
     *
     * @var float
     */
    public const DAYS_OF_A_YEAR = 365.25;

    /**
     * 儒略历历法废弃日期
     * 英帝国(含美国等): 1752年9月2日，
     * 其实这个废弃日期特别乱，根本不好统一计算，
     * 目前暂无完整数据，请不要修改该值
     *
     * @var int[]
     */
    public const JULIAN_ABANDONMENT_DATE = ['year' => 1582, 'month' => 10, 'day' => 4];

    /**
     * 格里历历法实施日期
     * 英帝国(含美国等): 1752年9月14日
     * 其实这个实施日期特别乱，根本不好统一计算，
     * 目前暂无完整数据，请不要修改该值
     *
     *
     * @var int[]
     */
    public const GREGORIAN_ADOPTION_DATE = ['year' => 1582, 'month' => 10, 'day' => 15];


    /**
     * 指定的年份是否是闰年
     * 如果指定的年份在self::JULIAN_ABANDONMENT_DATE年份之前或相同，则用儒略历的历法计算
     * 否则用格里的历法计算
     *
     * @param int $year 指定的年份
     *
     * @return bool true是闰年，false不是闰年
     */
    public static function isLeapYear(int $year): bool
    {
        if ((self::JULIAN_ABANDONMENT_DATE)['year'] <= $year) {
            return self::isJulianLeapYear($year);
        }

        return self::isGregorianLeapYear($year);
    }

    /**
     * 指定的儒略历年份是否是闰年
     * 儒略历平年二月是29天，闰年二月是30天
     *
     * @param int $year 指定的年份
     *
     * @return bool true是闰年，false不是闰年
     */
    protected static function isJulianLeapYear(int $year): bool
    {
        return $year % 4 == 0;
    }

    /**
     * 指定的格里历年份是否是闰年
     * 格里历平年二月是28天，闰年二月是29天
     *
     * @param int $year 指定的年份
     *
     * @return bool true是闰年，false不是闰年
     */
    protected static function isGregorianLeapYear(int $year): bool
    {
        return $year % 4 === 0 && ($year % 100 !== 0 || $year % 400 === 0);
    }

    /**
     * 计算TT时间的儒略日
     *
     * @param int $year        年
     * @param int $month       月 默认: 1
     * @param int $day         日 默认: 1
     * @param int $hours       时 默认: 12
     * @param int $minute      分 默认: 0
     * @param int $second      秒 默认: 0
     * @param int $millisecond 毫秒 默认: 0
     *
     * @return float 儒略日(JD)
     */
    public static function julianDay(int $year, int $month = 1, int $day = 1, int $hours = 12, int $minute = 0, int $second = 0,int $millisecond = 0): float
    {
        // 依据儒略历废弃日期和格里历实施日期，使用两个不同的公式计算儒略日
        if ($year < (self::JULIAN_ABANDONMENT_DATE)['year'] || ($year === (self::JULIAN_ABANDONMENT_DATE)['year'] && $month < (self::JULIAN_ABANDONMENT_DATE)['month']) || ($year === (self::JULIAN_ABANDONMENT_DATE)['year'] && $month === (self::JULIAN_ABANDONMENT_DATE)['month'] && $day <= (self::JULIAN_ABANDONMENT_DATE)['day'])) {
            // 儒略历日期
            $jd = self::julianDayInJulian($year, $month, $day, $hours, $minute, $second, $millisecond);
        } else if ($year > (self::GREGORIAN_ADOPTION_DATE)['year'] || ($year === (self::GREGORIAN_ADOPTION_DATE)['year'] && $month > (self::GREGORIAN_ADOPTION_DATE)['month']) || ($year === (self::GREGORIAN_ADOPTION_DATE)['year'] && $month === (self::GREGORIAN_ADOPTION_DATE)['month'] && $day >= (self::GREGORIAN_ADOPTION_DATE)['day'])) {
            // 格里历日期
            $jd = self::julianDayInGregorian($year, $month, $day, $hours, $minute, $second, $millisecond);
        }else{
            // 在儒略历废弃与格里历实施这中间有一段日期，这段日期的儒略日计算使用格里历实施的起始日计算
            $jd = self::julianDayInGregorian((self::GREGORIAN_ADOPTION_DATE)['year'], (self::GREGORIAN_ADOPTION_DATE)['month'], (self::GREGORIAN_ADOPTION_DATE)['day']);
        }

        return $jd;
    }

    /**
     * 儒略历日期(TT)转儒略日
     *
     * @param int $year        年
     * @param int $month       月 默认: 1
     * @param int $day         日 默认: 1
     * @param int $hours       时 默认: 12
     * @param int $minute      分 默认: 0
     * @param int $second      秒 默认: 0
     * @param int $millisecond 毫秒 默认: 0
     *
     * @return float 儒略日(JD)
     */
    private static function julianDayInJulian(int $year, int $month = 1, int $day = 1, int $hours = 12, int $minute = 0, int $second = 0, int $millisecond = 0): float
    {
        // 计算公式参见: https://zh.wikipedia.org/wiki/%E5%84%92%E7%95%A5%E6%97%A5
        // 或参见: https://blog.csdn.net/weixin_42763614/article/details/82880007

        // 算式适用于儒略历日期(中午12点 UT)
        // JDN表达式与JD的关系是: JDN = floor(JD + 0.5)

        $a = floor((14 - $month) / 12);
        $y = $year + 4800 - $a;
        $m = $month + 12 * $a - 3;
        $second += $millisecond / 1000.0;
        $d = $day + $hours / 24.0 + $minute / 1440.0 + $second / 86400.0;

        $jdn = $d + floor((153 * $m + 2) / 5) + 365 * $y + floor($y / 4) - 32083;

        return $jdn - 0.5; // jd值是JDN-0.5
    }

    /**
     * 格里历日期(TT)转儒略日
     *
     * @param int $year        年
     * @param int $month       月 默认: 1
     * @param int $day         日 默认: 1
     * @param int $hours       时 默认: 12
     * @param int $minute      分 默认: 0
     * @param int $second      秒 默认: 0
     * @param int $millisecond 毫秒 默认: 0
     *
     * @return float 儒略日(JD)
     */
    private static function julianDayInGregorian(int $year, int $month = 1, int $day = 1, int $hours = 12, int $minute = 0, int $second = 0, int $millisecond = 0): float
    {
        // 计算公式参见: https://zh.wikipedia.org/wiki/%E5%84%92%E7%95%A5%E6%97%A5
        // 或参见: https://blog.csdn.net/weixin_42763614/article/details/82880007

        // 算式适用于格里历日期(中午12点 UT)
        // JDN表达式与JD的关系是: JDN = floor(JD + 0.5)

        $a = floor((14 - $month) / 12);
        $y = $year + 4800 - $a;
        $m = $month + 12 * $a - 3;
        $second += $millisecond / 1000.0;
        $d = $day + $hours / 24.0 + $minute / 1440.0 + $second / 86400.0;

        $jdn = $d + floor((153 * $m + 2) / 5) + 365 * $y + floor($y / 4) - floor($y / 100) + floor($y / 400) - 32045;

        return (float)$jdn - 0.5; // jd值是JDN-0.5
    }

    /**
     * 简化的儒略日(Modified Julian Day, MJD)是将儒略日(Julian Day, JD)进行简化后得到的新计时法。
     * 1957年,简化儒略日由史密松天体物理台(Smithsonian Astrophysical Observatory)引入。
     * 1957年史密松天体物理台为便用于记录“伴侣号”人造卫星的轨道,将儒略日进行了简化，并将其命名为简化儒略日,其定义为: MJD=JD-2400000.5
     * 儒略日2400000是1858年11月16日,因为JD从中午开始计算,所以简化儒略日的定义中引入偏移量0.5,
     * 这意味着MJD 0相当于1858年11月17日的凌晨,并且每一个简化儒略日都在世界时午夜开始和结束.
     *
     *
     * @param int $year
     * @param int $month       月 默认: 1
     * @param int $day         日 默认: 1
     * @param int $hours       时 默认: 0
     * @param int $minute      分 默认: 0
     * @param int $second      秒 默认: 0
     * @param int $millisecond 毫秒 默认: 0
     *
     * @return float 简化的儒略日(MJD)
     */
    public static function modifiedJulianDay(int $year, int $month = 1, int $day = 1, int $hours = 0, int $minute = 0, int $second = 0, int $millisecond = 0): float
    {
        if(($year > 1858 || ($year === 1858 && $month > 11) || ($year === 1858 && $month === 11 && $day >= 17) )){
            $jd = self::julianDay($year, $month, $day, $hours, $minute, $second, $millisecond);
            $mjd = $jd - 2400000.5;
        }else{
            $mjd = (float)0;
        }
        return $mjd;
    }

    /**
     * 由简化的儒略日转成儒略日
     * 其实际是mjd + 2400000.5
     *
     * @param float $mjd 简化的儒略日(MJD)
     *
     * @return float 儒略日(JD)
     */
    public static function mjdTojulianDay(float $mjd):float
    {
        return $mjd + 2400000.5;
    }

    /**
     * 儒略日转DateTime
     * 注意: 该方法中$jd应为经过摄动值和deltaT调整后的jd,不需要对jd作时区调整
     * 在该方法的处理中，我们将UT==UTC
     *
     * @param float  $jd 儒略日
     * @param string $timeZoneName  php包DateTimeZone允许的timezone名称 默认: 'Asia/Shanghai'
     *
     * @return DateTime
     * @throws Exception
     */
    public static function jdToDateTime(float $jd, string $timeZoneName = 'Asia/Shanghai'):DateTime
    {
        // 我们将UT与UTC看作相等
        // 时区名称设置不正确时使用: 'Asia/Shanghai' (亚洲/上海，中国北京时间)
        try {
            $DateTimeZone = new \DateTimeZone(trim($timeZoneName));
        }catch(Exception $e) {
            // print $e->getMessage();
            $timeZoneName = 'Asia/Shanghai';
            $DateTimeZone = new \DateTimeZone($timeZoneName);
        }

        $dateArray = self::julianDayToDateArray($jd);
        $dt = new \DateTime(sprintf("%d-%d-%d %d:%d:%d.%d",$dateArray['Y'], $dateArray['n'], $dateArray['j'],$dateArray['G'], $dateArray['i'], $dateArray['s'], $dateArray['u']), new \DateTimeZone('UTC'));
        $dt->setTimezone($DateTimeZone);
        return $dt;
    }

    /**
     * 儒略日计算对应的日期时间(TT)
     *
     * @param float $jd 儒略日
     *
     * @return array 一个包含日期数据的数组，['Y' int年, 'n' int月, 'j' int日, 'G' int时, 'i' int分, 's' int秒, 'u' int毫秒]
     */
    public static function julianDayToDateArray(float $jd):array
    {
        $jdn = $jd + 0.5;

        // 计算公式: https://blog.csdn.net/weixin_42763614/article/details/82880007
        $Z = floor($jdn); // 儒略日的整数部分
        $F = $jdn - $Z; // 儒略日的小数部分

        // 2299161 是1582年10月15日12时0分0秒
        if($Z < self::JULIAN_GREGORIAN_BOUNDARY){
            //儒略历
            $A = $Z;
        }else{
            $a = floor(($Z - 2305507.25) / 36524.25);

            // 10 是格里历比儒略历多出来的10天，这个数应对儒略历应废弃日期与格里历实施日期的差数
            //$gregorian_adoption_time = gmmktime(12,0,0,(self::GREGORIAN_ADOPTION_DATE)['month'], (self::GREGORIAN_ADOPTION_DATE)['day'], (self::GREGORIAN_ADOPTION_DATE)['year']);
            //$julian_abandonment_time = gmmktime(12,0,0,(self::JULIAN_ABANDONMENT_DATE)['month'], (self::JULIAN_ABANDONMENT_DATE)['day'], (self::JULIAN_ABANDONMENT_DATE)['year']);
            //$IntervalDays = floor(($gregorian_adoption_time - $julian_abandonment_time) / 86400);
            $IntervalDays = 10;
            $A = $Z + $IntervalDays + $a - floor($a/4);
        }

        $dayF = (float)1;
        $E = (float)0;
        $k = 0;
        while (true){
            $B = $A + 1524; // 以BC4717年3月1日0时为历元
            $C = floor(($B - 122.1) / 365.25); // 积年
            $D = floor(365.25 * $C); // 积年的日数
            $E = floor(($B - $D) / 30.6); // B-D为年内积日，E即月数
            $dayF = $B - $D - floor(30.6 * $E) + $F;
            if($dayF >= 1) break; // 否则即在上一月，可前置一日重新计算
            $A -= 1;
            $k += 1;
        }

        $month = $E < 14 ? $E - 1 : $E - 13; // 月
        $year = $month > 2 ? $C - 4716 : $C - 4715; // 年
        $dayF += $k;
        if(intval($dayF,10) === 0) $dayF += 1;

        // 天数分开成天与时分秒
        $day = floor($dayF); // 天
        $dayD = $dayF - $day;
        $hh = $ii = $ss = $ms = (float)0;
        if($dayD > 0){
            $hhF = $dayD * 24;
            $hh  = floor($hhF); // 时
            $hhD = $hhF - $hh;
            if($hhD > 0){
                $iiF = $hhD * 60;
                $ii  = floor($iiF); // 分
                $iiD = $iiF - $ii;
                if($iiD > 0){
                    $ssF = $iiD * 60;
                    $ss  = floor($ssF); // 秒
                    $ssD = $ssF - $ss;
                    if($ssD > 0){
                        $ms = $ssD / 1000;
                    }
                }
            }
        }

        return [
            'Y' => (int)$year,       // 年
            'n' => (int)$month,      // 月
            'j' => (int)$day,        // 日
            'G' => (int)$hh,         // 时
            'i' => (int)$ii,         // 分
            's' => (int)$ss,         // 秒
            'u' => (int)$ms          // 毫秒
        ];
    }

    /**
     * TT时间儒略日转UTC时间的儒略日
     * 根据公式: TT = UTC + 64.184s
     *
     * @param float $jd TT时间的儒略日
     *
     * @return float UTC时间的儒略日
     */
    public static function julianDayUTC(float $jd):float
    {
        return  $jd - 64.184 / 86400.0;
    }

    /**
     * UTC时间的儒略日转TT时间的儒略日
     * 根据公式: TT = UTC + 64.184s
     *
     * @param float $utcJd
     *
     * @return float
     */
    public static function julianDayTT(float $utcJd):float
    {
        return  $utcJd + 64.184 / 86400.0;
    }

    /**
     * 计算标准历元起的儒略日
     *
     * @param float $jd TT时间的儒略日
     *
     * @return float 标准历元起的儒略日
     */
    public static function julianDayFromJ2000(float $jd):float
    {
        return $jd - self::JULIAN_DAY_J2000;
    }

    /**
     * 计算标准历元起的儒略世纪
     *
     * @param float $jd 要计算的儒略日
     *
     * @return float 儒略世纪数
     */
    public static function julianCentury(float $jd):float
    {
        return self::julianDayFromJ2000($jd) / self::DAYS_OF_A_YEAR / 100.0;
    }

    /**
     * 计算标准历元起的儒略千年数
     *
     * @param float $jd 要计算的儒略日
     *
     * @return float 儒略千年数
     */
    public static function julianThousandYear(float $jd):float
    {
        return self::julianDayFromJ2000($jd) / self::DAYS_OF_A_YEAR / 1000.0;
    }


}
