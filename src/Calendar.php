<?php
declare(strict_types=1);

namespace phpu\calendar;

use Exception;
use DateTime;
use DateTimeZone;
use DateInterval;
use DatePeriod;

class Calendar
{

    // 日历显示
    public const GRIDo_MONTH = 0; // 显示一个月的日历
    public const GRIDo_WEEK  = 1; // 显示一周的日历
    public const GRIDo_DAY   = 2; // 显示一天的日历


    /**
     * 使用的时区 DateTimeZone 对象
     * 默认为php.ini中设置的时区
     *
     * @var DateTimeZone|null
     */
    private $timezone = null;

    /**
     * 默认配置，该项是在读取config.php出错的情况下使用该默认值
     *
     * @var array
     */
    private $config = ['grid' => Calendar::GRIDo_MONTH,
        'solar_terms'       => true,
        'lunar'             => true,
        'heavenly_earthly'  => true,
        'night_zi_hour'     => false,
        'first_day_of_week' => 0
    ];


    /**
     * Calendar constructor.
     * @param DateTimeZone|null $timezone
     * @param string $configname 配置项
     */
    public function __construct(DateTimeZone $timezone=null, string $configname = 'default'){
        $this->timezone = $timezone;
        // 获取配置
        $this->loadConfig($configname);

    }

    /**
     * 对象拷贝
     */
    public function __clone(){
        $this->timezone = clone $this->timezone;
    }

    /**
     * 设置配置
     * @param string $configName
     * @return $this
     */
    public function setConfig(string $configName = 'default'){
        $this->loadConfig($configName);
        return $this;
    }



    /**
     * 整个日历数据
     * @param int $y 指定的年，-1000至3000
     * @param int $m 指定的月，1至12
     * @param int $d 指定的日，默认0
     * @param int $h 指定时间，默认 -1
     * @return array|false
     */
    public function getCalendar(int $y,int $m,int $d=0,int $h=-1){
        if($y < -1000 || $y > 3000 || $m< 1 || $m > 12){
            return false;
        }
        $currentDt = $this->now();
        list($currentY,$currentM,$currentD,$currentH,$currentI,$currentS,$currentW)
            = array_map(function($v){return intval($v,10);}, explode(',',$currentDt->format('Y,n,j,G,i,s,w')));
        if($d === 0){
            // 未指定日，默认为1日
            $d = 1;
            // 如果年月都与当前年月相同，而日又未指定，则使用当前的日。
            if($m === $currentM && $y === $currentY){
                $d = $currentD;
            }
        }

        // 未指定小时，默认为当前时间的小时数。
        if($h < 0){
            $h = $currentH;
        }


        // 新建一个 DateTime
        $newDt = $this->newDateTime(sprintf('%04.0f-%02.0f-%02.0f %02.0f:%02.0f:%02.0f', $y, $m, $d, $h, $currentI, $currentS));

        return [
            'y' => $y,
            'm' => $m,
            'd' => $d,
            'h' => $h,
            'timezone' => $newDt->getTimezone()->getName(),
            'offset' => $newDt->getOffset(),
            'days' => $this->days($newDt)
        ];
    }

    /**
     * 以字符串新建一个DateTime
     *
     * @param string $timeStr 字符串格式与DateTime构造时所需相同，参考 https://php.net/manual/en/datetime.formats.php
     * @return DateTime|false
     */
    private function newDateTime(string $timeStr){
        try {
            return new DateTime($timeStr, $this->timezone);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 当前时间的 DateTime 对象
     * @return DateTime
     */
    private function now():DateTime{
        return $this->newDateTime('now');
    }


    /**
     * 取日期数据
     * @param DateTime $dt
     * @return array
     */
    private function days(DateTime $dt){

        list($y,$m,$d,$h,$i,$s,$w)
            = array_map(function($v){return intval($v,10);}, explode(',',$dt->format('Y,n,j,G,i,s,w')));

        // 取当年节气
        $yearSolarTerms = null;
        if($this->config['solar_terms']){
            $yearSolarTerms = Date::solarTerms($dt);
        }

        $dtClone = clone $dt;


        $grid = $this->config['grid'];

        // 按周或月取日期
        if($grid === self::GRIDo_WEEK || $grid === self::GRIDo_MONTH){

            $recurrences = 0;

            if ($grid === self::GRIDo_WEEK){
                $recurrences = 6; // 循环6次,取7天(含开始日期)
            }else{
                $recurrences = 41; // 循环41次,取42天(含开始日期)
                if ($d > 1){
                    $dtClone->sub(DateInterval::createFromDateString(($d-1).' day')); // 当月的1日
                }
            }

            // 开始日期
            $startDt = clone $dtClone;

            $w = (int)$startDt->format('w');
            $first_day_of_week = $this->config['first_day_of_week'];
            $subdays = 0;
            if($w >= $first_day_of_week){
                $subdays = $w - $first_day_of_week;
            }else{
                $subdays = 7 - $first_day_of_week + $w;
            }

            if ($subdays > 0) {
                $startDt->sub(DateInterval::createFromDateString($subdays.' day'));
            }

            $dateRange = new DatePeriod(
                $startDt, // 开始日期
                new DateInterval('P1D'), // 递增1天
                (int)$recurrences
            );
        }else{
            // 只取一天的日期
            $dateRange = [$dtClone];
        }

        $resultDate = [];
        foreach($dateRange as $date){
            $DateFormat = $date->format('Y,n,j,G,i,s,w');
            list($y,$m,$d,$h,$i,$s,$w) = explode(',',$DateFormat,7);
            $key = $y.'-'.$m.'-'.$d;
            $resultDate[$key]['gregorian'] = [
                'y' => (int)$y,
                'm' => (int)$m,
                'd' => (int)$d,
                'h' => (int)$h,
                'w' => (int)$w
            ];

            if ((int)$y == 1582 && (int)$m == 10 && ((int)$d > 4 && (int)$d < 15)){
                // todo 格里历不存在这个日期
            }else{
                // 节气
                if($yearSolarTerms && isset($yearSolarTerms[$y.'-'.$m.'-'.$d])){
                    $resultDate[$key]['solar_terms'] = $yearSolarTerms[$y.'-'.$m.'-'.$d];
                }
                // 农历
                if($this->config['lunar']){
                    $resultDate[$key]['lunar'] = Date::gregorianToLunar($date);
                }
                // 干支
                if($this->config['heavenly_earthly']){
                    $resultDate[$key]['gz'] = Date::sexagenaryCycle($date);
                }
            }
        }

        return $resultDate;
    }





    /**
     * 获取配置
     * @param string $name
     * @return array
     */
    private function loadConfig(string $name='default'):array{
        $configFile = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
        if (is_file($configFile)) {
            $con = include $configFile;
            if (is_array($con)){
                $this->config = $con['default'];
                if ($name !== 'default' && isset($con[$name])){
                    $this->config = array_merge($this->config, array_change_key_case($con[$name]));
                }
            }
        }

        $first_day_of_week = abs(intval($this->config['first_day_of_week'],10));
        $this->config['first_day_of_week'] = $first_day_of_week % 7;

        $result = $this->config;
        return $result;
    }
}