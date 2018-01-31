<?php
/**
 * Created by PhpStorm.
 * User: linlinwang
 * Date: 2017/1/5 0005
 * Time: 12:26
 */
public function actionGetrank($appId = 0, $type = 1) {
    if (!MyFunction::funCheckThread("stats getrank --appId={$appId} --type={$type}"))
        return;

    $iphoneKey = array(
        '0' => array(
            'userAgent' => "AppStore/2.0 iOS/8.4 model/iPhone7,2 build/12H143 (6; dt:106)",
            'httpHeader' => array('X-Disd' => "1221203839", 'X-Apple-Store-Front' => "143465-19,29 ab:aF574PC1 t:native"),
            'cookie' => "wosid-lite=xUw7nQwXbm4JVpcZvlNCQ0; ndcd=wc1.1.w-855182.1.2.wMPvRa5D_shpNlCj5roo-Q%2C%2C.wmKzW2WpKrsGfWjcN-61XwK52D5P5XL_sP7IpBjNzZ5m6csSSMNnZhqYxRBLTIUdbYtsOfFkm3VbudpLTmrncTl1cCTD1k-yJiacAaZk5xN09bIe6opEil9GAY_CoLVjtsUeJWMOBicAuFr9pxzhxAoNy4vxLXNm6dlkuum8oo8%2C; mz_at_ssl-1221203839=AwUAAAFqAAE6EAAAAABXMbtdzy8W2BczmuRY4Ev+2/DnIcFFSLc=; amia-1221203839=1wccA3O9dMEzewvhC3fiNn6fXCWlBl/jWXLGOonymemCuxLxk6Bbko3gR+r94lcckaZ0uVYfCgUbSBoC1D45zw==; mzf_in=502449; mt-tkn-1221203839=AooaLG13fYHEI9gy860bbQIIDt9PXkuWqSooPEC6XX6vzjFy4MwLdsLC6RADiOYFMnyB2F+jyEbrLEzrimhuY4k8KGw2T+YB2mEB2QwHffI7jt7pRAe7pqBPn+s60Gl3gSItG9Fg6opALnJxVpe4v6fPkfP11Dws/ujGNpkt6Niwv3DMVOLFSmpGL11CIuQfHrfEx4M=; itspod=50; xp_ci=3z2TQeWAzGnuz4RizCaZzvQAZxX3q; xt-b-ts-1221203839=1462781189567; mz_at0-1221203839=AwQAAAFqAAE6EAAAAABXMEUE/jzKFYF8vXJWYIhKwhZagFStPJA=; mt-asn-1221203839=5; hsaccnt=1; X-Dsid=1221203839; amp=TElg/7Ozr4dRajX3UN/fm0tTsZ7Rg7cEC9rWgvxN7+ZFN/7JZV6s83FXSn4SwCEFvrCB38RJOfNgFbWDDnxwj4iIgYNyXlIC8voWBTCXkStyUQ6kknRk58ybvrdm1EwDrxuWMlJLJti5Glh2UNbd2/gMjqAJe1He9DK6997D4Gs=; xt-src=b",
        ),
        '1' => array(
            'userAgent' => "AppStore/2.0 iOS/8.4 model/iPhone7,2 build/12H143 (6; dt:106)",
            'httpHeader' => array('X-Disd' => "1221203839", 'X-Apple-Store-Front' => "143465-19,29 ab:aF574PC1 t:native"),
            'cookie' => "wosid-lite=xUw7nQwXbm4JVpcZvlNCQ0; ndcd=wc1.1.w-855182.1.2.wMPvRa5D_shpNlCj5roo-Q%2C%2C.wmKzW2WpKrsGfWjcN-61XwK52D5P5XL_sP7IpBjNzZ5m6csSSMNnZhqYxRBLTIUdbYtsOfFkm3VbudpLTmrncTl1cCTD1k-yJiacAaZk5xN09bIe6opEil9GAY_CoLVjtsUeJWMOBicAuFr9pxzhxAoNy4vxLXNm6dlkuum8oo8%2C; mz_at_ssl-1221203839=AwUAAAFqAAE6EAAAAABXMbtdzy8W2BczmuRY4Ev+2/DnIcFFSLc=; amia-1221203839=1wccA3O9dMEzewvhC3fiNn6fXCWlBl/jWXLGOonymemCuxLxk6Bbko3gR+r94lcckaZ0uVYfCgUbSBoC1D45zw==; mzf_in=502449; mt-tkn-1221203839=AooaLG13fYHEI9gy860bbQIIDt9PXkuWqSooPEC6XX6vzjFy4MwLdsLC6RADiOYFMnyB2F+jyEbrLEzrimhuY4k8KGw2T+YB2mEB2QwHffI7jt7pRAe7pqBPn+s60Gl3gSItG9Fg6opALnJxVpe4v6fPkfP11Dws/ujGNpkt6Niwv3DMVOLFSmpGL11CIuQfHrfEx4M=; itspod=50; xp_ci=3z2TQeWAzGnuz4RizCaZzvQAZxX3q; xt-b-ts-1221203839=1462781189567; mz_at0-1221203839=AwQAAAFqAAE6EAAAAABXMEUE/jzKFYF8vXJWYIhKwhZagFStPJA=; mt-asn-1221203839=5; hsaccnt=1; X-Dsid=1221203839; amp=TElg/7Ozr4dRajX3UN/fm0tTsZ7Rg7cEC9rWgvxN7+ZFN/7JZV6s83FXSn4SwCEFvrCB38RJOfNgFbWDDnxwj4iIgYNyXlIC8voWBTCXkStyUQ6kknRk58ybvrdm1EwDrxuWMlJLJti5Glh2UNbd2/gMjqAJe1He9DK6997D4Gs=; xt-src=b",
        ),
        '2' => array(
            'userAgent' => "AppStore/2.0 iOS/8.4 model/iPhone7,2 build/12H143 (6; dt:106)",
            'httpHeader' => array('X-Disd' => "1221203839", 'X-Apple-Store-Front' => "143465-19,29 ab:aF574PC1 t:native"),
            'cookie' => "wosid-lite=xUw7nQwXbm4JVpcZvlNCQ0; ndcd=wc1.1.w-855182.1.2.wMPvRa5D_shpNlCj5roo-Q%2C%2C.wmKzW2WpKrsGfWjcN-61XwK52D5P5XL_sP7IpBjNzZ5m6csSSMNnZhqYxRBLTIUdbYtsOfFkm3VbudpLTmrncTl1cCTD1k-yJiacAaZk5xN09bIe6opEil9GAY_CoLVjtsUeJWMOBicAuFr9pxzhxAoNy4vxLXNm6dlkuum8oo8%2C; mz_at_ssl-1221203839=AwUAAAFqAAE6EAAAAABXMbtdzy8W2BczmuRY4Ev+2/DnIcFFSLc=; amia-1221203839=1wccA3O9dMEzewvhC3fiNn6fXCWlBl/jWXLGOonymemCuxLxk6Bbko3gR+r94lcckaZ0uVYfCgUbSBoC1D45zw==; mzf_in=502449; mt-tkn-1221203839=AooaLG13fYHEI9gy860bbQIIDt9PXkuWqSooPEC6XX6vzjFy4MwLdsLC6RADiOYFMnyB2F+jyEbrLEzrimhuY4k8KGw2T+YB2mEB2QwHffI7jt7pRAe7pqBPn+s60Gl3gSItG9Fg6opALnJxVpe4v6fPkfP11Dws/ujGNpkt6Niwv3DMVOLFSmpGL11CIuQfHrfEx4M=; itspod=50; xp_ci=3z2TQeWAzGnuz4RizCaZzvQAZxX3q; xt-b-ts-1221203839=1462781189567; mz_at0-1221203839=AwQAAAFqAAE6EAAAAABXMEUE/jzKFYF8vXJWYIhKwhZagFStPJA=; mt-asn-1221203839=5; hsaccnt=1; X-Dsid=1221203839; amp=TElg/7Ozr4dRajX3UN/fm0tTsZ7Rg7cEC9rWgvxN7+ZFN/7JZV6s83FXSn4SwCEFvrCB38RJOfNgFbWDDnxwj4iIgYNyXlIC8voWBTCXkStyUQ6kknRk58ybvrdm1EwDrxuWMlJLJti5Glh2UNbd2/gMjqAJe1He9DK6997D4Gs=; xt-src=b",
        ),
    );
    $finalResult = array();
    $replaceSql = array();
    $time = time();
    $date = date('Y-m-d H:00:00', $time);

    $hour = date('G', $time);
    if ($hour >= 0 && $hour <= 8) {
        die();
    }


    //得到应用的关键词
    $keywordList = Keywords::model()->findAll("`app_id`='{$appId}' AND `app_type`='{$type}'");

    if ($keywordList) {
        foreach ($keywordList as $key => $value) {

            $key = rand(0, count($iphoneKey) - 1);
            $para = $iphoneKey[$key];

            $result = MyFunction::searchWordsResult($value['keyword'], $para);

            if ($result[0]['results']) {
                $rank = 9999;
                foreach ($result[0]['results'] as $k => $v) {
                    if ($v['id'] == $value['app_id']) {
                        $rank = $k + 1;
                        break;
                    }
                }

                Yii::app()->db->createCommand("UPDATE `keywords` SET `rank`='{$rank}' WHERE `id`='{$value['id']}'")->execute();

                $finalResult[$value['id']] = $rank;
                //echo date('Y-m-d H:i:s') . $value['keyword'] . "查询完成\n";
            } else {
                echo date('Y-m-d H:i:s') . $value['keyword'] . "没有数据\n";
            }
        }
    }

    if ($finalResult) {
        foreach ($finalResult as $key => $value) {
            array_push($replaceSql, "('{$date}','{$appId}','{$key}','{$value}')");
        }
    }

    if (count($replaceSql) > 0) {
        $executeSql = "REPLACE INTO `app_{$appId}` (`time`,`app_id`,`word_id`,`ranking`) VALUES ";
        $executeSql .= join(' , ', $replaceSql);
        Yii::app()->db_collect->createCommand($executeSql)->execute();
    }
    //echo date('Y-m-d H:i:s') . '应用' . $appId . "统计完成\n";
}

function searchWordsResult($keyWord, $para) {
    $res = MyFunction::curl_request('https://itunes.apple.com/WebObjects/MZStore.woa/wa/search?clientApplication=Software&term=' . urlencode($keyWord), $para);
    if ($res != null) {
        $res = json_decode($res, true);
        if (isset($res['pageData']['bubbles']) && $res['pageData']['bubbles'] != null) {
            $rs = $res['pageData']['bubbles'];
            return $rs;
        }
        return $res;
    } else {
        return false;
    }
}

function curl_request($url, $para) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);

    if (false === isset($para['header'])) {
        $para['header'] = false;
    } else {
        $para['header'] = true;
    }
    curl_setopt($curl, CURLOPT_HEADER, $para['header']);


    if (false === isset($para['location'])) {
        $para['location'] = false;
    } else {
        $para['location'] = true;
    }
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $para['location']);

    unset($para['location']);


    if (true === isset($para['cookie'])) {
        $cookieString = '';
        $cookieTmpArr = array();
        if (is_array($para['cookie'])) {
            foreach ($para['cookie'] as $cookieKey => $cookieItem) {
                $cookieTmpArr[] = $cookieKey . '=' . $cookieItem;
            }
            $cookieString = implode(';', $cookieTmpArr);
        } else {
            $cookieString = $para['cookie'];
        }
        //echo $cookieString;
        curl_setopt($curl, CURLOPT_COOKIE, $cookieString);
    }

    if (true === isset($para['userAgent'])) {
        curl_setopt($curl, CURLOPT_USERAGENT, $para['userAgent']);
    }
    if (true === isset($para['httpHeader'])) {
        $httpHeader = array();
        if (is_array($para['httpHeader'])) {
            foreach ($para['httpHeader'] as $headerKey => $headerItem) {
                $httpHeader[] = $headerKey . ":" . $headerItem;
            }
        } else {
            $httpHeader[] = $para['httpHeader'];
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeader);
    }

    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    if (substr($url, 0, 5) == 'https') {
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    }
    $data = curl_exec($curl);

    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    return $data;
}