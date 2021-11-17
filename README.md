# 日历

calendar、日历、中国农历、阴历、节气、干支、生肖、星座

通过天文计算和民间推算方法，准确计算出公历-1000年至3000年的农历、干支、节气等，同时支持多配置、多语言、多时区。

* 干支年以立春开始
* 干支月以节分隔
* 干支日可以设置是否区早晚子时
* 干支时从上一天的23点开始。


> 天文计算方法参考Jean Meeus的《Astronomical Algorithms》、[NASA](https://eclipse.gsfc.nasa.gov/SEhelp/deltatpoly2004.html "NASA")网站、[天文与历法](http://www.bieyu.com/ "天文与历法")网站等相关的天文历法计算方法。

> 注意：该程序视UT = UTC


- [Installation 安装](#installation-安装)
- [示例](#示例)
  - [日历](#日历)
  - [日历配置](#日历配置)
  - [儒略日与公历互换](#儒略日与公历互换)
  - [节气](#节气)
  - [农历与公历互换](#农历与公历互换)
  - [公历转换干支生肖](#公历转换干支生肖)
  - [星座](#星座)
- [帮助](https://github.com/liujiawm/php-calendar)
- 联系
  - QQ:194088
  - Email:liujiawm@msn.com

## Installation 安装 ##

```
composer require phpu/calendar
```


## 示例 ##

### 日历 ###

日历默认时区是: Asia/Shanghai

```
当天：
【五 2020-5-15 农历:2020年四月廿三 干支:庚子年辛巳月戊午日】

日历表：
日 2020-4-26 农历:2020年四月初四 干支:庚子年庚辰月己亥日
一 2020-4-27 农历:2020年四月初五 干支:庚子年庚辰月庚子日
二 2020-4-28 农历:2020年四月初六 干支:庚子年庚辰月辛丑日
三 2020-4-29 农历:2020年四月初七 干支:庚子年庚辰月壬寅日
四 2020-4-30 农历:2020年四月初八 干支:庚子年庚辰月癸卯日
五 2020-5-1 农历:2020年四月初九 干支:庚子年庚辰月甲辰日
六 2020-5-2 农历:2020年四月初十 干支:庚子年庚辰月乙巳日
日 2020-5-3 农历:2020年四月十一 干支:庚子年庚辰月丙午日
一 2020-5-4 农历:2020年四月十二 干支:庚子年庚辰月丁未日
二 2020-5-5 农历:2020年四月十三 干支:庚子年辛巳月戊申日 节气:立夏 定:08:50:58
三 2020-5-6 农历:2020年四月十四 干支:庚子年辛巳月己酉日
四 2020-5-7 农历:2020年四月十五 干支:庚子年辛巳月庚戌日
五 2020-5-8 农历:2020年四月十六 干支:庚子年辛巳月辛亥日
六 2020-5-9 农历:2020年四月十七 干支:庚子年辛巳月壬子日
日 2020-5-10 农历:2020年四月十八 干支:庚子年辛巳月癸丑日
一 2020-5-11 农历:2020年四月十九 干支:庚子年辛巳月甲寅日
二 2020-5-12 农历:2020年四月二十 干支:庚子年辛巳月乙卯日
三 2020-5-13 农历:2020年四月廿一 干支:庚子年辛巳月丙辰日
四 2020-5-14 农历:2020年四月廿二 干支:庚子年辛巳月丁巳日
五 2020-5-15 农历:2020年四月廿三 干支:庚子年辛巳月戊午日
六 2020-5-16 农历:2020年四月廿四 干支:庚子年辛巳月己未日
日 2020-5-17 农历:2020年四月廿五 干支:庚子年辛巳月庚申日
一 2020-5-18 农历:2020年四月廿六 干支:庚子年辛巳月辛酉日
二 2020-5-19 农历:2020年四月廿七 干支:庚子年辛巳月壬戌日
三 2020-5-20 农历:2020年四月廿八 干支:庚子年辛巳月癸亥日 节气:小满 定:21:48:35
四 2020-5-21 农历:2020年四月廿九 干支:庚子年辛巳月甲子日
五 2020-5-22 农历:2020年四月三十 干支:庚子年辛巳月乙丑日
六 2020-5-23 农历:2020年(闰)四月初一 干支:庚子年辛巳月丙寅日
日 2020-5-24 农历:2020年(闰)四月初二 干支:庚子年辛巳月丁卯日
一 2020-5-25 农历:2020年(闰)四月初三 干支:庚子年辛巳月戊辰日
二 2020-5-26 农历:2020年(闰)四月初四 干支:庚子年辛巳月己巳日
三 2020-5-27 农历:2020年(闰)四月初五 干支:庚子年辛巳月庚午日
四 2020-5-28 农历:2020年(闰)四月初六 干支:庚子年辛巳月辛未日
五 2020-5-29 农历:2020年(闰)四月初七 干支:庚子年辛巳月壬申日
六 2020-5-30 农历:2020年(闰)四月初八 干支:庚子年辛巳月癸酉日
日 2020-5-31 农历:2020年(闰)四月初九 干支:庚子年辛巳月甲戌日
一 2020-6-1 农历:2020年(闰)四月初十 干支:庚子年辛巳月乙亥日
二 2020-6-2 农历:2020年(闰)四月十一 干支:庚子年辛巳月丙子日
三 2020-6-3 农历:2020年(闰)四月十二 干支:庚子年辛巳月丁丑日
四 2020-6-4 农历:2020年(闰)四月十三 干支:庚子年辛巳月戊寅日
五 2020-6-5 农历:2020年(闰)四月十四 干支:庚子年壬午月己卯日 节气:芒种 定:12:57:52
六 2020-6-6 农历:2020年(闰)四月十五 干支:庚子年壬午月庚辰日
```

上例代码:
```
// 新的日历对象
$Calendar = new phpu\calendar\Calendar();

// 创建一个日历，createCalendar方法4个参数分别是:int年,int月,int日=0,int时=-1
// createCalendar方法在指定的年份不在-1000至3000之内时throw DomainException异常
$calendar = $Calendar->createCalendar(2020,5,15);

// ------------ 以下代码部分处理当天的日期转换 --------------- //

$current_gregorian_str = $calendar['w'] . ' ' . $calendar['y'] . '-' . $calendar['m'] . '-' . $calendar['d'];

$current_jq_str = !empty($calendar['solar_terms']) ? ' 节气:' . $calendar['solar_terms'][0] . ' 定:' . $calendar['solar_terms'][1] : '';

$current_gz_str = !empty($calendar['gz']) ? $calendar['gz']['y']['s'] . '年'.$calendar['gz']['m']['s'] . '月'.$calendar['gz']['d']['s'] . '日' : '';

$current_lunar_str = !empty($calendar['lunar']) ? $calendar['lunar'][0] . '年' . $calendar['lunar'][1] . $calendar['lunar'][2] : '';

print '当天：' . "\n";

print '【' . $current_gregorian_str . ' 农历:' . $current_lunar_str . ' 干支:' . $current_gz_str . $current_jq_str . '】'."\n";

print "\n";

// ------------ 以下部分是日历表数据转换 --------------- //

print '日历表：' . "\n";

foreach ($calendar['days'] as [$k,$day]){
    // 公历
    $gregorian_str = $day['gregorian']['w'] . ' ' . $day['gregorian']['y'] . '-' . $day['gregorian']['m'] . '-' . $day['gregorian']['d'];

    // 节气
    $jq_str = (isset($day['solar_terms']) && isset($day['solar_terms'][$k])) ? ' 节气:' . $day['solar_terms'][$k][0] . ' 定:' . $day['solar_terms'][$k][1] : '';

    // 干支
    $gz_str = !empty($day['gz']) ? $day['gz']['y']['s'] . '年' . $day['gz']['m']['s'] . '月' . $day['gz']['d']['s'] . '日' : '';

    // 农历
    $lunar_str = !empty($day['lunar']) ? $day['lunar'][0] . '年' . $day['lunar'][1] . $day['lunar'][2] : '';

    print $gregorian_str . ' 农历:' . $lunar_str .' 干支:'. $gz_str . $jq_str . "\n";

}


```

### 日历配置 ###

`config.php`

```
// 默认配置
    'default' => [

        // 读取日历长度
        // 0 GRID_DAY 一天
        // 1 GRID_WEEK 一周
        // 2 GRID_MONTH 一个月
        'grid' => \phpu\calendar\Calendar::GRID_MONTH,

        // 读取节气
        'solar_terms' => true,

        // 读取干支
        'heavenly_earthly' => true,

        // 读取农历
        'lunar' => true,

        // 区分早晚子时，true则 23:00-24:00 00:00-01:00为子时，否则00:00-02:00为子时
        'night_zi_hour' => false,
    ],
    // 自定义多个配置
    // 在new对象时加上参数键名作为配置名
    // 使用方法: $Calendar = new Calendar('demo');
    'demo'=>[
        'grid' => 1,
        'solar_terms' => true,
        'heavenly_earthly' => false,
        'lunar' => false,
    ],

```

引入配置:

```
// 方法一
// 如果要引入'demo'这个配置，则可以在new对象时，在第一个参数处实参名称'demo'
$Calendar = new Calendar('demo');

// 方法二
// 在使用过程中单独设置
$Calendar = new Calendar();
$calendar = $Calendar->setConfig('demo')->createCalendar(2020,5,15);
```

引入语言:

> 语言文件中在设置时区名称，引入语言后自动设置了时区

```
// 方法一
// 如果要引入'zh-tw',可以在new对象时，在第二个参数处实参'zh-tw'
$Calendar = new Calendar(null, 'zh-tw');

// 方法二
$Calendar = new Calendar();
$calendar = $Calendar->setLang('zh-tw')->createCalendar(2020,5,15);

```

设置时区:

如果需要单独设置时区:

```
// 因为在加载语言文件时，根据语言文件中的时区名称设置了时区，
// 如果修改语言文件中默认的时区时setTimeZone要在setLang之后使用。
$calendar = $Calendar->setLang('zh-tw')->setTimeZone('Asia/Shanghai')->createCalendar(2020,5,15);

```

### 儒略日与公历互换 ###

`Julian`

儒略日转换为日期时间(TT):

```
// Julian::julianDay参数(int年,int月=1,int日=1,int时=12,int分=0,int秒=0,int毫秒=0)
$jd = phpu\calendar\Julian::julianDay(2011,8,9,5,24,35);
var_dump($jd);
```

日期时间(TT)转换为儒略日:

```
$jd = 2455782.7254051;

// Julian::julianDayToDate参数(float儒略日)
// 返回一个array,是字符串索引的整数值['Y' int年, 'n' int月, 'j' int日, 'G' int时, 'i' int分, 's' int秒,'u' int毫秒]
$date = phpu\calendar\Julian::julianDayToDateArray($jd);
printf('%d-%d-%d %d:%d:%d',$date['Y'],$date['n'],$date['j'],$date['G'],$date['i'],$date['s']);

print "\n";

// 或者使用jdToDateTime方法直接转换为DateTime
// 该方法第二个参数为时区名称，默认为: 'Asia/Shanghai'
$dateTime = phpu\calendar\Julian::jdToDateTime($jd, 'Asia/Shanghai');
print $dateTime->format(\DateTimeInterface::RFC3339)
```

简化儒略日MJD

> 简化儒略日计算1858年11月16日午夜之后的日期

```
// 日期直接转换成简化儒略日
// 如果日期在1858年11月17日凌晨之前，则返回0
$mjd = phpu\calendar\Julian::modifiedJulianDay(2011,8,9,5,24,35);
print $mjd;

print "\n";

// 简化儒略日转儒略日
$jd = phpu\calendar\Julian::mjdTojulianDay($mjd);
print $jd;

```

### 节气 ###

`SolarTerm`

显示一整年的节气，同时显示上一年最后一个气(冬至)和下一年第一个节(小寒)

如2021年全年节气显示如下:

```
冬至: 2020-12-21T18:02:36+08:00 
小寒: 2021-01-05T11:23:50+08:00 
大寒: 2021-01-20T04:40:31+08:00 
立春: 2021-02-03T22:59:23+08:00 
雨水: 2021-02-18T18:44:29+08:00 
惊蛰: 2021-03-05T16:53:57+08:00 
春分: 2021-03-20T17:37:28+08:00 
清明: 2021-04-04T21:34:48+08:00 
谷雨: 2021-04-20T04:32:43+08:00 
立夏: 2021-05-05T14:46:29+08:00 
小满: 2021-05-21T03:36:22+08:00 
芒种: 2021-06-05T18:51:32+08:00 
夏至: 2021-06-21T11:31:47+08:00 
小暑: 2021-07-07T05:05:28+08:00 
大暑: 2021-07-22T22:26:42+08:00 
立秋: 2021-08-07T14:54:28+08:00 
处暑: 2021-08-23T05:35:23+08:00 
白露: 2021-09-07T17:53:16+08:00 
秋分: 2021-09-23T03:20:56+08:00 
寒露: 2021-10-08T09:38:45+08:00 
霜降: 2021-10-23T12:50:30+08:00 
立冬: 2021-11-07T12:58:14+08:00 
小雪: 2021-11-22T10:33:05+08:00 
大雪: 2021-12-07T05:56:49+08:00 
冬至: 2021-12-21T23:59:05+08:00 
小寒: 2022-01-05T17:14:07+08:00 

```
取节气测试代码:

```
$st_names = ['春分', '清明', '谷雨', '立夏', '小满', '芒种', '夏至', '小暑', '大暑', '立秋', '处暑', '白露',
                          '秋分', '寒露', '霜降', '立冬', '小雪', '大雪', '冬至', '小寒', '大寒', '立春', '雨水', '惊蛰'];

// 该方法第二个参数为时区名称，默认为: 'Asia/Shanghai'
$sts = phpu\calendar\SolarTerm::solarTerms(2021);

foreach ($sts as $stv){
    printf("%s: %s \n", $st_names[$stv['i']], $stv['d']->format(\DateTimeInterface::RFC3339));
}

```

### 农历与公历互换 ###

[农历中文数字表示参考下面的两个函数](#农历中文数字表示可以参考以下两个函数)

```
公历 2020-05-26 是农历: 2020年 (闰)4月 4日 

农历2020年(闰)4月4是公历: 2020-05-26
```

```
$year = 2020;
$month = 5;
$day = 26;

$lunarDateArray = ChineseCalendar::gregorianToLunar($year,$month,$day);
$leapstr = $lunarDateArray['leap'] === 1 ? '(闰)' : '';
printf("公历 %'.04d-%'.02d-%'.02d 是农历: %'.04d年 %s%d月 %d日 \n", $year, $month, $day, $lunarDateArray['Y'], $leapstr,$lunarDateArray['n'],$lunarDateArray['j']);
print "\n";

// 默认时区: 'Asia/Shanghai'
$gdate = \phpu\calendar\ChineseCalendar::lunarToGregorian($lunarDateArray['Y'],$lunarDateArray['n'],$lunarDateArray['j'],$lunarDateArray['leap']);
print '农历'.$lunarDateArray['Y'].'年'.$leapstr.$lunarDateArray['n'].'月'.$lunarDateArray['j'].'是公历' . ': ' . $gdate->format('Y-m-d') ."\n";
print "\n";

```

### 农历中文数字表示可以参考以下两个函数 ###

以下两个函数在`Calendar`类中作为私有方法存在，因此在日历显示时会根据语言自动转换

```
/**
 * 农历月份数转中文表示
 *
 * @param int $month  农历月份数
 * @param int $isLeap 是否闰月
 *
 * @return string
 */
function lunarMonthChinese(int $month, int $isLeap = 0):string
{
    $lunar_leap = '(闰)';
    $lunar_months = ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'];
    
    if($month < 1 || $month > 12){
        return '';
    }

    $leapstr = $isLeap ? $lunar_leap : '';

    return $leapstr . $lunar_months[$month - 1];
}

/**
 * 农历日数字转中文表示
 *
 * @param int $day 农历的日数
 *
 * @return string 中文表示法 如：初五，初十，二十，廿五
 */
function lunarDayChinese(int $day):string
{
    $lunar_number = ['日', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十'];
    $lunar_whole_tens = ['初', '十', '廿'];
    
    
    // 农历每月的天数不能超过30
    if ($day < 1 || $day > 30){
        return '';
    }

    $daystr = '';

    switch ($day){
        case 10 : $daystr = $lunar_whole_tens[0] . $lunar_number[10]; // 初十
            break;
        case 20 : $daystr = $lunar_number[2] . $lunar_number[10];     // 二十
            break;
        case 30 : $daystr = $lunar_number[3] . $lunar_number[10];     // 三十
            break;
        default:
            $k = $day / 10;
            $m = $day % 10;
            $daystr = $lunar_whole_tens[$k] . $lunar_number[$m];
    }

    return $daystr;
}

```

### 公历转换干支生肖 ###

> 生肖依地支为引索

```
2020年5月26日19时的干支是: 庚子(鼠)年 辛巳月 己巳日 甲戌时
```

```
$year = 2020;
$month = 5;
$day = 26;
$hours = 19;
$scs = \phpu\calendar\ChineseCalendar::sexagenaryCycle($year, $month, $day, $hours);
printf("%d年%d月%d日%d时的干支是: %s%s(%s)年 %s%s月 %s%s日 %s%s时 \n",$year,$month,$day,$hours,
    $heavenly_stems[$scs['y']['g']],$earthly_branches[$scs['y']['z']],$symbolic_animals[$scs['y']['z']],
    $heavenly_stems[$scs['m']['g']],$earthly_branches[$scs['m']['z']],
    $heavenly_stems[$scs['d']['g']],$earthly_branches[$scs['d']['z']],
    $heavenly_stems[$scs['h']['g']],$earthly_branches[$scs['h']['z']]);
```

### 星座 ###

```
5月26日出生 属双子座
```

```
$month = 5;
$day = 26;
$star_sign = ['水瓶', '双鱼', '白羊', '金牛', '双子', '巨蟹', '狮子', '处女', '天秤', '天蝎', '射手', '摩羯'];
$signIndex = \phpu\calendar\ChineseCalendar::signIndex($month, $day);
printf("%d月%d日出生 属%s座", $month, $day, $star_sign[$signIndex]);
```

