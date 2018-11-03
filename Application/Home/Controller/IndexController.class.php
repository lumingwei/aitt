<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        set_time_limit(0);
    	//获取价格 
    	//https://c0.3.cn/stock?skuId=7765111&cat=670,671,672&venderId=1000000157&area=1_72_2799_0&buyNum=1&choseSuitSkuIds=&extraParam={%22originid%22:%221%22}&ch=1&fqsp=0&pduid=1080223807&pdpin=&callback=jQuery1369985
    	$skuids = $this->getSkuIds();
        if(!empty($skuids)){
        	foreach($skuids as $sku){
        		$list[] = [
        			'id'=>$sku,
        			'price'=>$this->getPrice($sku),
        			'title'=>$this->getTitle($sku)
        		];
    
        	}
        }
 
                $file_name = '京东商品实时价格';
                $xls_head = array(
                    '京东skuid',
                    'sku名称',
                    '价格'
                );
                header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
                header("Accept-Ranges: bytes");
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Disposition: attachment; filename={$file_name}" . date('Y-m-d H:i:s',time()) . ".csv");
                header("Content-Transfer-Encoding: binary");
                echo mb_convert_encoding(implode(",", $xls_head) ."\r\n", 'GBK', 'UTF-8');
                foreach ($list as $row)
                {
                    $item = array();
                    $item[] = $row['id'].'`';
                    $item[] = $row['title'];
                    $item[] = $row['price'];
                    echo mb_convert_encoding(implode(",", $item) ."\r\n", 'GBK', 'UTF-8');
                }
    }

    public function getPrice($sku_id){        		    	
    	$price_url = "https://c0.3.cn/stock?skuId=".$sku_id."&cat=670,671,672&venderId=1000000157&area=1_72_2799_0&buyNum=1&choseSuitSkuIds=&extraParam={%22originid%22:%221%22}&ch=1&fqsp=0&pduid=1080223807&pdpin=&callback=jQuery1369985";
    	    $price = 0;
	    	$cont = file_get_contents($price_url);
	    	if(!empty($cont)){
	    		$tmp = explode('jQuery1369985(', $cont);
	    		if(!empty($tmp[1])){
	    			$tmp1 = rtrim($tmp[1],')');
	    		}
	    		
	    		if(!empty($tmp1)){
	    			$tmp1 = iconv("GBK","UTF-8",$tmp1);
	    			$json = json_decode($tmp1);
	    			$price = !empty($json->stock->jdPrice->p)?$json->stock->jdPrice->p:0;
	    		}     		    		
	    	}
	    	return $price;
    }
        public function getTitle($sku_id){        		    	
    	    $price_url = "https://item.jd.com/{$sku_id}.html";
    	    $title = '';
	    	$cont = file_get_contents($price_url);
	    	if(!empty($cont)){
	    		$regex4="/<div class=\"sku-name\".*?>.*?<\/div>/ism";  
			if(preg_match_all($regex4, $cont, $matches)){  
			   if(!empty($matches[0][0])){
			   	    $tmp = explode('<div class="sku-name">', $matches[0][0]);
			   	    $tmp1 = rtrim($tmp[1],'</div>');
			   	    $title = iconv("GBK","UTF-8",$tmp1);
                    $title = preg_replace("/<.*>/","",$title);
			   	    $title = trim($title);
			   }
			}
	    	return $title;
           }
        }

        public function getSkuIds(){
            //达能
            $sku[] = 831721;
            $sku[] = 1216716;
            $sku[] = 1171691;
            $sku[] = 1216715;
            $sku[] = 1014489;
            $sku[] = 1217836;
            $sku[] = 831713;
            $sku[] = 1279473;
            $sku[] = 3722856;
            $sku[] = 873282;
            $sku[] = 4264348;
            $sku[] = 4264346;
            $sku[] = 4264358;
            $sku[] = 4264350;

            //雀巢
            $sku[] = 4396232;
            $sku[] = 4396142;
            $sku[] = 3849865;
            $sku[] = 4209173;
            $sku[] = 4713594;
            $sku[] = 4008365;
            $sku[] = 4713572;
            $sku[] = 4712764;
            $sku[] = 4712778;
            $sku[] = 4007911;
            $sku[] = 4712762;
            $sku[] = 4712736;
            $sku[] = 4007909;
            $sku[] = 1080961;
            $sku[] = 1080962;
            $sku[] = 6493773;
            $sku[] = 6493789;
            $sku[] = 6493791;
            $sku[] = 255780;
            $sku[] = 255778;
            $sku[] = 255751;
            $sku[] = 1194389;

            //雅培
            $sku[] = 252591;
            $sku[] = 2363423;
            $sku[] = 2362923;
            $sku[] = 1462651;
            $sku[] = 813925;
            $sku[] = 1000728;
            $sku[] = 1568949;
            $sku[] = 4847760;
            $sku[] = 6089690;
            $sku[] = 4262984;
            $sku[] = 4550506;
            $sku[] = 100000334678;
            $sku[] = 100000539598;
            return $sku;
        }

}