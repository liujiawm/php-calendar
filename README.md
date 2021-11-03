# php-calendar
a php calendar api 一个用php写的日历
有节气、农历、干支、星座等

## INSTALL 按装

### composer

```
composer require phpu/calendar
```

## DEMO 1 简单例子2(一个完整的日历数据)

```
// $Calendar = new \phpu\calendar\Calendar(new DateTimeZone('Asia/Shanghai'),'default'); // 自定义时区和配置项
$Calendar = new Calendar(); // 默认系统时区，默认配置项
var_dump($Calendar->getCalendar(2020,1,30));
// 如需json输出，可以直接将数组转成json
// echo json_encode($Calendar->getCalendar(2020,1,30));

```

## DEMO 2 简单例子2(公历转农历)

```
lunar = \phpu\calendar\Date::gregorianToLunar(new DateTime('2020-1-30'));
var_dump($lunar);
```

## DEMO 3 简单例子3(农历转公历)

```
$gregorian = \phpu\calendar\Date::lunarToGregorian(2020, 1, 6, 0, new DateTimeZone('Asia/Shanghai'));
var_dump($gregorian);
```

其它更多的功能请参看源代码。

### config 配置
```
// 读取日历长度
        // 0 GRIDO_MONTH 一个月
        // 1 GRIDO_WEEK 一周
        // 2 GRIDO_DAY 一天
        'grid' => \phpu\calendar\Calendar::GRIDO_MONTH,

        // 读取节气
        'solar_terms' => true,

        // 读取农历
        'lunar' => true,

        // 读取干支
        'heavenly_earthly' => true,

        // 区分早晚子时，true则 23:00-24:00 00:00-01:00为子时，否则00:00-02:00为子时
        'night_zi_hour' => false,

        // 日历显示时第一列显示周几，(日历表第一列是周几,0周日,依次最大值6)
        'first_day_of_week' => 0,

```

### 数据索引对应的各文字数组

数组顺序不能改变

zh
```
// 24节气名词
    'solar_terms' => ['春分', '清明', '谷雨', '立夏', '小满', '芒种', '夏至', '小暑', '大暑', '立秋', '处暑', '白露',
        '秋分', '寒露', '霜降', '立冬', '小雪', '大雪', '冬至', '小寒', '大寒', '立春', '雨水', '惊蛰'],

    // 10天干
    'heavenly_stems' => ['甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸'],

    // 12地支
    'earthly_branches' => ['子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'],

    // 生肖
    'symbolic_animals' => ['鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪'],

    // 星座
    'zodiac' => ['水瓶', '双鱼', '白羊', '金牛', '双子', '巨蟹', '狮子', '处女', '天秤', '天蝎', '射手', '摩羯'],

    // 五行
    'five_phases' => ['金','木','水','火','土'],

```

ko
```
// 24节气名词
    'solar_terms' => ['춘분', '청명', '곡우', '입하', '소만', '망종', '하지', '소서', '대서', '입추', '처서', '백로',
        '추분', '한로', '상강', '입동', '소설', '대설', '동지', '소한', '대한', '입춘', '우수', '경칩'],

    // 10天干
    'heavenly_stems' => ['갑', '을', '병', '정', '무', '기', '경', '신', '임', '계'],

    // 12地支
    'earthly_branches' => ['자', '축', '인', '묘', '진', '사', '오', '미', '신', '유', '술', '해'],

    // 生肖
    'symbolic_animals' => ['자', '축', '인', '묘', '진', '사', '오', '미', '신', '유', '술', '해'],

    // 星座
    'zodiac' => ['보병', '쌍어', '백양', '금우', '쌍아', '거해', '사자', '처녀', '천칭', '천갈', '인마', '마갈'],

    // 五行
    'five_phases' => ['金','木','水','火','土'],

```

jp
```
// 24节气名词
    'solar_terms' => ['春分', '清明', '谷雨', '立夏', '小満', '芒種', '夏至', '小暑', '大暑', '立秋', '処暑', '白露',
        '秋分', '寒露', '霜降', '立冬', '小雪', '大雪', '冬至', '小寒', '大寒', '立春', '雨水', '啓蟄'],

    // 10天干
    'heavenly_stems' => ['甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸'],

    // 12地支
    'earthly_branches' => ['子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'],

    // 生肖
    'symbolic_animals' => ['鼠', '牛', '虎', '兎', '竜', '蛇', '馬', '羊', '猿', '鶏', '犬', '豚'],

    // 星座
    'zodiac' => ['宝瓶', '双魚', '白羊', '金牛', '双児', '巨蟹', '獅子', '処女', '天秤', '天蝎', '人馬', '磨羯'],

    // 五行
    'five_phases' => ['金','木','水','火','土'],

```

vi
```
// 24节气名词
    'solar_terms' => ['Xuân phân', 'Thanh minh', 'Cốc vũ', 'Lập hạ', 'Tiểu mãn', 'Mang chủng', 'Hạ chí', 'Tiểu thử', 'Đại thử', 'Lập thu', 'Xử thử', 'Bạch lộ',
        'Thu phân', 'Hàn lộ', 'Sương giáng', 'Lập đông', 'Tiểu tuyết', 'Đại tuyết', 'Đông chí', 'Tiểu hàn', 'Đại hàn', 'Lập xuân', 'Vũ thủy', 'Kinh trập'],

    // 10天干
    'heavenly_stems' => ['giáp', 'ất', 'bính', 'đinh', 'mậu', 'kỷ', 'canh', 'tân', 'nhâm', 'quý'],

    // 12地支
    'earthly_branches' => ['Tý', 'Sửu', 'Dần', 'Thố', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'],

    // 生肖
    'symbolic_animals' => ['Tý', 'Sửu', 'Dần', 'Thố', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'],

    // 星座
    'zodiac' => ['Bảo Bình', 'Song Ngư', 'Bạch Dương', 'Kim Ngưu', 'Song Tử', 'Cự Giải', 'Sư Tử', 'Xử Nữ', 'Thiên Bình', 'Thiên Yết', 'Nhân Mã', 'Ma Kết'],

    // 五行
    'five_phases' => ['Kim','Mộc','Thủy','Hỏa','Thổ'],

```

en
```
// 24节气名词
    'solar_terms' => ['Vernal Equinox', 'Clear and Bright', 'Grain Rain', 'Start of Summer',
        'Small Full', 'Grain in Ear', 'Summer Solstice', 'Minor Heat',
        'Major Heat', 'Start of Autumn', 'Limit of Heat', 'White Dew',
        'Autumnal Equinox', 'Cold Dew', 'Frost Descent', 'Start of Winter',
        'Minor Snow', 'Major Snow', 'Winter Solstice', 'Minor Cold',
        'Major Cold', 'Start of Spring', 'Rain Water', 'Awakening of Insects'],

    // 10天干
    'heavenly_stems' => ['甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸'],

    // 12地支
    'earthly_branches' => ['子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'],

    // 生肖
    'symbolic_animals' => ['Rat', 'Ox', 'Tiger', 'Rabbit', 'Dragon', 'Snake', 'Horse', 'Goat', 'Monkey', 'Rooster', 'Dog', 'Pig'],

    // 星座
    'zodiac' => ['Aquarius', 'Pisces', 'Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn'],

    // 五行
    'five_phases' => ['metal','wood','water','fire','earth'],

```
