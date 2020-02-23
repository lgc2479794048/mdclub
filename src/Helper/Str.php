<?php

declare(strict_types=1);

namespace MDClub\Helper;

use HTMLPurifier;
use HTMLPurifier_Config;
use Markdownify\Converter;
use Parsedown;

/**
 * 字符串相关方法
 */
class Str
{
    /**
     * 下划线转驼峰
     *
     * @param  string $str
     * @param  string $separator
     * @return string
     */
    public static function toCamelize($str, $separator = '_')
    {
        $str = $separator . str_replace($separator, ' ', strtolower($str));

        return ltrim(str_replace(' ', '', ucwords($str)), $separator);
    }

    /**
     * 生成 guid
     *
     * @return string
     */
    public static function guid(): string
    {
        if (function_exists('com_create_guid')) {
            $guid = trim(com_create_guid(), '{}');

            return strtolower(str_replace('-', '', $guid));
        }

        mt_srand((int)microtime() * 10000);

        return md5(uniqid((string)mt_rand(), true));
    }

    /**
     * 获取随机字符串
     *
     * @param  int     $length  字符串长度
     * @param  string  $chars   字符的集合
     * @return string
     */
    public static function random(int $length, string $chars = ''): string
    {
        if (!$chars) {
            $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        }

        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $result;
    }

    /**
     * HTML 转 Markdown
     *
     * @param  string $html  HTML 字符串
     * @return string        Markdown 字符串
     */
    public static function toMarkdown(string $html): string
    {
        if (!$html) {
            return $html;
        }

        static $converter;
        if ($converter === null) {
            $converter = new Converter();
        }

        return $converter->parseString($html);
    }

    /**
     * Markdown 转 HTML
     *
     * @param  string $markdown  Markdown 字符串
     * @return string            HTML 字符串
     */
    public static function toHtml(string $markdown): string
    {
        if (!$markdown) {
            return $markdown;
        }

        static $parsedown;
        if ($parsedown === null) {
            $parsedown = new Parsedown();
        }

        return $parsedown->text($markdown);
    }

    /**
     * 过滤 HTML
     *
     * @param  string  $html  需要过滤的字符串
     * @return string         过滤后的字符串
     */
    public static function removeXss(string $html): string
    {
        if (!$html) {
            return $html;
        }

        $allow_tags = [
            'a[href][target][title][rel]',
            'abbr[title]',
            'address',
            'b',
            'big',
            'blockquote[cite]',
            'br',
            'caption',
            'center',
            'cite',
            'code',
            'col[align][valign][span][width]',
            'colgroup[align][valign][span][width]',
            'dd',
            'del',
            'div',
            'dl',
            'dt',
            'em',
            'font[color][size][face]',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'hr',
            'i',
            'img[src][alt][title][width][height]',
            'ins',
            'li',
            'ol',
            'p',
            'pre',
            's',
            'small',
            'span',
            'sub',
            'sup',
            'strong',
            'table[width][border][align][valign]',
            'tbody[align][valign]',
            'td[width][rowspan][colspan][align][valign]',
            'tfoot[align][valign]',
            'th[width][rowspan][colspan][align][valign]',
            'thead[align][valign]',
            'tr[align][valign]',
            'tt',
            'u',
            'ul',
            'figure',
            'figcaption',
        ];

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', implode(',', $allow_tags));
        $def = $config->getHTMLDefinition(true);
        $def->addElement('audio', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
            'autoplay' => 'Bool',
            'controls' => 'Bool',
            'loop'     => 'Bool',
            'preload'  => 'Bool',
            'src'      => 'URI',
        ]);
        $def->addElement('figure', 'Block', 'Flow', 'Common');
        $def->addElement('figcaption', 'Block', 'Flow', 'Common');
        $def->addElement('mark', 'Inline', 'Inline', 'Common');
        $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
            'autoplay' => 'Bool',
            'controls' => 'Bool',
            'loop'     => 'Bool',
            'preload'  => 'Bool',
            'src'      => 'URI',
            'height'   => 'Length',
            'width'    => 'Length',
        ]);

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($html);
    }
}