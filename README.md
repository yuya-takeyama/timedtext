TimedText
=========

文字列を時間によって出し分けるためのお手軽構文.  
非エンジニア向けの CMS などに.

構文
----

```
ここは常に出力されます
{before 2012-03-04 22:00}
ここは 2012-03-04 22:00 になるまでの間出力されます
{/before}
{after 2012-03-04 22:00}
ここは 2012-03-04 22:00 になったら出力されます
{/after}
```

使い方
------

```php
<?php
require_once 'TimedText.php';

$input =<<< __INPUT__
ここは常に出力されます
{before 2012-03-04 22:00}
ここは 2012-03-04 22:00 になるまでの間出力されます
{/before}
{after 2012-03-04 22:00}
ここは 2012-03-04 22:00 になったら出力されます
{/after}
__INPUT__;

echo TimedText::convert($input);
```

作者
----

Yuya Takeyama [http://yuyat.jp/](http://yuyat.jp/)
