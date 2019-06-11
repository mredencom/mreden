<?php
/**
 **http://blog.csdn.net/blueheart20 
 ** 首先遍历获取文章列表，提取每篇博客的地址
 ** 訪問這些地址需要 模擬不同ip 進行訪問
 ** 将博客中所有文章访问一遍，从而达到刷访问量
 **/

   echo "grepping URLs..." . PHP_EOL;
//正则，用来提取页面中的博客地址
//            $pattern = '/\<span class="link_title"\>\<a href="\/u011816231\/article\/details\/\d{7,8}"\>/';
            $pattern = '<a href="https://blog.csdn.net/u011816231/article/details/\d{7,8}" target="_blank">';
//循环遍历所文章列表，提取文章URL，循环次数为博客实际的分页数
            for ($i = 1; $i < 11; $i++) {
                $url  = "https://blog.csdn.net/u011816231/article/list/$i";
                $html = file_get_contents($url);
//                pd($html);
                preg_match_all($pattern, $html, $arr);
//                pd($arr);
                if ($i == 1) {
                    $list = $arr[0];
                } else {
                    //将每个分页中提取的URL合并到一个大数组中，方便处理
                    $list = array_merge($list, $arr[0]);
                }
//                pd($list);
            }
//            pd($list);
//从提取结果中获得最终的文章地址
            $urllist = [];
//            $pattern = '/\/u011816231\/article\/details\/\d{7,8}/';
            $pattern = "/href=\"(\S*)\"/";
            foreach ($list as $value) {
//                pd($value);
                preg_match($pattern, $value, $result);
//                preg_match_all($pattern, $value, $result);
//                pd($result);
//                $urllist[] = "http://blog.csdn.net" . $result[0];
                $urllist[] = $result[1];
            }
            echo "grep URLs finshed. Total URL numbers: " . count($urllist) . PHP_EOL;
//            pd($urllist);
//循环访问次数
            $count = 100;
            $j     = 0;
            for ($i = 1; $i < $count + 1; $i++) {
                foreach ($urllist as $value) {
//                    pd($value);
                    //第一种方式
//                    $curl = curl_init($value);
//                    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//                        'Client_Ip: ' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255),
//                    ));
//                    $str = curl_exec($curl);
//                    curl_close($curl);
                    //第二种方式
//                     $ip = rand(1, 255).".".rand(1, 255).".".rand(1, 255).".".rand(1, 255);
//                     $headers = array("X-FORWARDED-FOR:$ip");
//                     $curl = curl_init($value);
//                     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//                     $src = curl_exec($curl);
//                     curl_close($curl);
                    //第三种

                    $header = array(
                        'CLIENT-IP: ' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255),
                        'X-FORWARDED-FOR:' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255)
                    );
                    $ch     = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $value);
                    // 关闭SSL验证
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([]));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                    // 模拟来源
                    curl_setopt($ch, CURLOPT_REFERER, "https://www.csdn.net/");

                    $response = curl_exec($ch);

                    if ($error = curl_error($ch)) {
                        die($error);
                    }

                    curl_close($ch);
//                    p(json_encode($response));
                    echo substr(json_encode($response), 0, 10) . "j=" . $j++, PHP_EOL;
                }
                echo "loop time: $i" . PHP_EOL;
            }

/*******************************************************************************************/

?>
