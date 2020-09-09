<?php

use Mashinka\Commands\CommandInterface;

use http\Client\Curl;
//#!/bin/bash
//JEKYLL_ENV=production jekyll build -q
//
//echo 'Step 2'
//cd _site && tar -czf b.tar.gz *
//
//echo 'Step 3'
//curl -i https://api.selcdn.ru/auth/v1.0 -H "X-Auth-User:${SEL_USER}" -H "X-Auth-Key:${SEL_PASS}"
//
//echo 'Step 4'
//shopt -s extglob
//
//while IFS=':' read -r key value; do
//    value=${value##+([[:space:]])}; value=${value%%+([[:space:]])}
//
//    case "$key" in
//        x-auth-token*) SEL_TOKEN="$value"
//;;
//     esac
//done < <(curl -i -s https://api.selcdn.ru/auth/v1.0 -H "X-Auth-User:${SEL_USER}" -H "X-Auth-Key:${SEL_PASS}")
//
//echo "Step 5 = $SEL_TOKEN"
//curl -i -XPUT --progress-bar -s https://api.selcdn.ru/v1/SEL_"${SEL_ACCOUNT}"/"${SEL_CONTAINER}"/?extract-archive=tar.gz -H "X-Auth-Token: ${SEL_TOKEN}" -T $BLOG_BUILD_PATH/b.tar.gz
//
// echo 'Step 6'
// rm -rf $BLOG_BUILD_PATH
//
//echo "Finish. Press any key."
//read


class Deploy implements CommandInterface
{
    public function run()
    {
    }
}
