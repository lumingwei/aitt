<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function getGoodsImg($id = 0){
            $sp_url = "https://item.jd.hk/{$id}.html";
            $common_url = "https://item.jd.com/{$id}.html";
            $img = '';
            $cont = file_get_contents($sp_url);
            if(!empty($cont)){
            preg_match_all('/<img data-img="1" width="450" height="450".*?src="(.*?)".*?>/is',$cont,$array);
           if(!empty($array[1][0])){
                $img = 'https:'.$array[1][0];
           }
            return $img;        
    }
}
    public function getimg(){
        //http://176.122.166.164/index.php?m=Home&c=Index&a=getimg&id=2210567&title=小十二最小十二最棒的&price=666
        $id    = I('id','','trim');
        $title = I('title','','trim');
        $price = I('price','','trim');
        if(empty($id) || empty($title)){
            exit('参数不完整！');
        }
        $goods_img = $this->getGoodsImg($id);
        if(empty($price)){
              $price     = $this->getPrice($id);
        }
        if(empty($goods_img) || empty($price)){
            exit('信息获取失败');
        }
        $price = $price/100*100;        
        header("Content-type: image/png");
        $font = realpath("./msyh.ttf"); //写的文字用到的字体。
        $font1 = realpath("./msyh.ttf"); //写的文字用到的字体。
        $image_1 = @imagecreatefromjpeg($goods_img);  
        $image_2 = @imagecreatefromjpeg(realpath("./di.jpg"));  
        $max_X=imagesx($image_2);
        $max_Y=imagesy($image_2);  
        imagecopyresized($image_2,$image_1,$max_X/11.2,$max_Y/12,0,0,190,190,imagesx($image_1),imagesy($image_1));
      

        //从图片建立文件，此处以jpg文件格式为例
        $black = imagecolorallocate($image_2, 0, 0, 0);
        $red   = imagecolorallocate($image_2, 254,72,93);
        $text  = $title; //要写到图上的文字
        $text2 = $price;
        $text2 = '　'.$price;
        $text3 = '￥';
        $max_X = $max_X-10;
        $max_Y = $max_Y-10;
        $fontWidth1 = 13;//要把文字左右间距也有考虑进去
        $fontWidth2 = 20;//要把文字左右间距也有考虑进去
        $fontWidth3 = 16;//要把文字左右间距也有考虑进去
        $x1         = $this->getX($text,$fontWidth1,$max_X,$spacing=4);
        $x2         = $this->getX($text2,$fontWidth2,$max_X*0.40,$spacing=5);
        //$x3         = $this->getX($text3,$fontWidth3,$max_X,$spacing=6);
        imagettftext($image_2, $fontWidth1, 0, $x1, $max_Y*0.81, $black, $font1, $text);
        imagettftext($image_2, $fontWidth2, 0, $x2, $max_Y*0.965, $red, $font, $text2);
        imagettftext($image_2, $fontWidth3, 0, $x2+8, $max_Y*0.965, $red, $font, $text3);

        imagepng($image_2);
        imagedestroy($image_2);        
    }
    private function getX($text, $fontSize, $max_X, $spacing)
    {
        $newStr    = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $text);
        $jj1       = mb_strlen($newStr, "utf-8");
        $jj1       = !empty($jj1) ? $jj1 : 0;
        $jj2       = mb_strlen($text, 'utf-8') - $jj1;
        $textWidth = $fontSize * mb_strlen($text, 'utf-8') + ($jj1 - $jj2) * $spacing;
        $x1        = ceil(($max_X - $textWidth) / 2); //计算文字的水平位置
        return $x1;
    }

    public function index(){
        $uid = I('uid','1','intval');
        $list = M('Sku')->where(['uid'=>$uid])->select();   
        $this->assign('list',$list); 
        $this->display();
    }

    public function del_sku(){
        $sku_id = I('sku_id','','trim');
        if(empty($sku_id)){
            $this->error('参数异常',U('Index/index'));
        }
        M('Sku')->where(['sku_id'=>$sku_id])->delete();
        $this->success('操作成功',U('Index/index'));
    } 

    public function add_sku(){
          $this->display();
    }

    public function add_sku_save(){
           $extra_msg = '';
           $sku_ids = I('sku_ids','','trim');
           if(empty($sku_ids)){
             $this->error('参数异常',U('Index/index'));
           }
           $sku_ids = explode(',', $sku_ids);
           if(empty($sku_ids)){
             $this->error('参数异常',U('Index/index'));
           }
           $where['sku_id'] = ['IN',$sku_ids];
           $exists = M('Sku')->where($where)->getField('sku_id',true);
           $exists = !empty($exists)?$exists:[];
           $add_skus =  array_diff($sku_ids, $exists);
           if(!empty($add_skus)){
              foreach ($add_skus as $value) {
                  $add_data[] = [
                     'sku_id'=>$value
                  ];
              }
              M('Sku')->addAll($add_data);
           }
           if(!empty($add_skus)){
              $extra_msg .= ' <br/> 以下sku成功入库:['.implode(',',$add_skus).']';
           }                
           if(!empty($exists)){
              $extra_msg .= ' <br/> 以下sku已经入库，无需重复添加:['.implode(',', $exists).']';
           }
           $this->success('操作成功'.$extra_msg,U('Index/index'),4);
    }  

    public function getExcel(){
        set_time_limit(0);
        $t1 = time();
    	//获取价格 
    	//https://c0.3.cn/stock?skuId=7765111&cat=670,671,672&venderId=1000000157&area=1_72_2799_0&buyNum=1&choseSuitSkuIds=&extraParam={%22originid%22:%221%22}&ch=1&fqsp=0&pduid=1080223807&pdpin=&callback=jQuery1369985
        $easy = I('easy',0,'intval');
    	$sku_ids = I('sku_ids','','trim');
        if(!empty($easy)){
            if(empty($sku_ids)){
                $this->error('参数异常',U('Index/easy'));
            }else{
                  $sku_ids = explode("\n", $sku_ids);
            }
        }
        if(!empty($sku_ids) && is_array($sku_ids)){
            foreach ($sku_ids as  $value) {
                if(!empty($value)){
                    $skuids[] = trim($value);
                }
            }
        }
        if(empty($easy)){
            $skuids = $this->getSkuIds();
        }
        if(!empty($skuids)){
        	foreach($skuids as $sku){
        		$list[] = [
        			'id'=>$sku,
        			'price'=>$this->getPrice($sku),
                    'title'=>$this->getTitle($sku),
        			'fullcut'=> $this->getFullCut($sku),
        		];
        	}
             $t2 = time();
             M("Time")->add(['start'=>$t1,'end'=>$t2,'time'=>$t2-$t1,'ids'=>implode(',',$skuids)]);
        }
 
                $file_name = '京东商品实时价格';
                $xls_head = array(
                    '京东skuid',
                    'sku名称',
                    '价格',
                    '满减信息',
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
                    $item[] = trim($row['fullcut']);
                    echo mb_convert_encoding(implode(",", $item) ."\r\n", 'GBK', 'UTF-8');
                }
    }

    public function getFullCut($sku_id){                      
        $fullcut_url = "https://cd.jd.com/promotion/v2?callback=jQuery5431379&skuId=".$sku_id."&area=1_72_4137_0&shopId=1000001582&venderId=1000001582&cat=1319%2C1523%2C7052&isCanUseDQ=isCanUseDQ-1&isCanUseJQ=isCanUseJQ-1&platform=0&orgType=2&jdPrice=760.00&appid=1&_=1541471660594";
            $full_cut = '';
            $cont = file_get_contents($fullcut_url);
            if(!empty($cont)){
                $tmp = explode('jQuery5431379(', $cont);
                if(!empty($tmp[1])){
                    $tmp1 = rtrim($tmp[1],')');
                }
                if(!empty($tmp1)){
                    $tmp1 = iconv("GBK","UTF-8",$tmp1);
                    $json = json_decode($tmp1);
                    $counpons = $json->skuCoupon;
                    $pros = $json->prom->pickOneTag;
                     if(!empty($pros)){
                        foreach ($pros as $v) {
                            if($v->code == 15 && !empty($v->content)){
                                 $cut_pros[] = $v->content;
                            }
                        }
                    }                   
                    if(!empty($counpons)){
                        foreach ($counpons as $value) {
                            if($value->couponType == 1 && !empty($value->trueDiscount) && !empty($value->quota)){
                                 $cut_coupons[] = '满'.$value->quota.'减'.$value->trueDiscount."({$value->name}，{$value->timeDesc})";
                            }
                        }
                    }
                }                       
            }
            $full_cut .= !empty($cut_coupons)    ? "优惠券满减 【".implode('，', $cut_coupons)."】 |--| ": '';
            $full_cut    .= !empty($cut_pros)    ? "促销满减 【".implode('，', $cut_pros)."】": '';
            return $full_cut;
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
            $uid = I('uid','1','intval');
            $sku = M('Sku')->where(['uid'=>$uid])->getField('sku_id',true);  
            return !empty($sku)?$sku:[];              
            //达能
            if(!isset($_GET['brand']) || $_GET['brand'] == 1){
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
            }

      if(!isset($_GET['brand']) || $_GET['brand'] == 2){
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
      }

   if(!isset($_GET['brand']) || $_GET['brand'] == 3){
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
            $sku[] = 1883303;
            $sku[] = 1883300;
            $sku[] = 1130828;
            $sku[] = 252586;
            $sku[] = 625421;
            $sku[] = 1130828;
            $sku[] = 1462644;
            $sku[] = 7425776;
            $sku[] = 4929097;
            $sku[] = 7640021;
            $sku[] = 6255999;
            $sku[] = 4251739;
            $sku[] = 7640005;
            $sku[] = 5915751;
            $sku[] = 2362911;
            $sku[] = 5422808;
            $sku[] = 333374;
            $sku[] = 5422810;
            $sku[] = 5176336;
            $sku[] = 6256055;
   }

     //美素佳儿
     $sku[] = 6514054;
     $sku[] = 6514056;
     $sku[] = 6514034;
     $sku[] = 8444261;
     $sku[] = 8444259;
     $sku[] = 8232352;


      //美赞臣
     $sku[] = 1431731;
     $sku[] = 1431727;


            return $sku;
        }

}