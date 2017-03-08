<?php
/**
 **http://blog.csdn.net/blueheart20 
 ** 首先遍历获取文章列表，提取每篇博客的地址
 ** 訪問這些地址需要 模擬不同ip 進行訪問
 ** 将博客中所有文章访问一遍，从而达到刷访问量
 **/

echo "grepping URLs..." . PHP_EOL;
//正则，用来提取页面中的博客地址
$pattern = '/\<span class="link_title"\>\<a href="\/blueheart20\/article\/details\/\d{7,8}"\>/';
//循环遍历所文章列表，提取文章URL，循环次数为博客实际的分页数
for ($i = 1; $i < 11; $i++) {
	$url = "http://blog.csdn.net/blueheart20/article/list/$i";
	$html = file_get_contents($url);
	preg_match_all($pattern, $html, $arr);
	if ($i == 1) {
		$list = $arr[0];
	} else {
		//将每个分页中提取的URL合并到一个大数组中，方便处理
		$list = array_merge($list, $arr[0]);
	}
}

//从提取结果中获得最终的文章地址
$pattern = '/\/blueheart20\/article\/details\/\d{7,8}/';
foreach ($list as $value) {
	preg_match($pattern, $value, $result);
	$urllist[] = "http://blog.csdn.net" . $result[0];
}

echo "grep URLs finshed. Total URL numbers: " . count($urllist) . PHP_EOL;


//循环访问次数
$count = 100;
for ($i = 1; $i < $count + 1; $i++) {
	foreach ($urllist as $value) {
		//第一种方式
		$curl = curl_init($value);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Client_Ip: ' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255),
		));
		$str = curl_exec($curl);
		curl_close($curl);
		//第二种方式
		// $ip = rand(1, 255).".".rand(1, 255).".".rand(1, 255).".".rand(1, 255);
		// $headers = array("X-FORWARDED-FOR:$ip");
		// $curl = curl_init($value);
		// curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		// $src = curl_exec($curl);
		// curl_close($curl);
	}
	echo "loop time: $i" . PHP_EOL;
}

/*******************************************************************************************/

?>