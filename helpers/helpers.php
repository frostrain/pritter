<?php

/**
 * 生成推特相关的url地址.
 * @param string $type
 * @param mixed $options
 * @return string 链接地址
 */
function twitter_url($type, $options)
{
    $host = 'https://twitter.com';
    switch ($type) {
    case 'u':
    case 'user':
    case 'm':
    case 'user_mention':
        // 这里的 $options 是用户的 screen_name
        $url = $host.'/'.$options;
        break;
    case 's':
    case 'status':
        $screen_name = $options['screen_name'];
        $status_id = $options['status_id'];
        $url = "{$host}/{$sceen_name}/status/{$status_id}";
        break;
    case 'h':
    case 'hashtag':
        // 这里的 $options 是 hashtag 的名称
        $url = $host.'/hashtag/'.$options;
        break;
    default:
        throw new \InvalidArugmentException("未知的twitter网址类型: $type");
    }

    return $url;
}