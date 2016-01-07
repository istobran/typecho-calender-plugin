<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET');
header('Access-Control-Allow-Credentials:true');
header("Content-Type: application/json;charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (!isset($_POST["year"]) || empty($_POST["year"])
		|| !isset($_POST["month"]) || empty($_POST["month"])
		|| !isset($_POST["action"]) || empty($_POST["action"])
		|| !isset($_POST["selected"]) || empty($_POST["selected"])) {
		echo '{"success":false,"tbody":"参数错误，转换信息不全", "year":null, "month":null}';
		return;
	} else {
		if ($_POST["selected"] == "year") {
			if ($_POST["action"] == "left") {
				//切换到上一年
				$tgt_time = mktime(0, 0, 0, $_POST["month"], 1, $_POST["year"] - 1);
			} elseif ($_POST["action"] == "right") {
				//切换到下一年
				$tgt_time = mktime(0, 0, 0, $_POST["month"], 1, $_POST["year"] + 1);
			}
		} elseif ($_POST["selected"] == "month") {
			if ($_POST["action"] == "left") {
				//切换到上个月
				$tgt_time = mktime(0, 0, 0, $_POST["month"] - 1, 1, $_POST["year"]);
			} elseif ($_POST["action"] == "right") {
				//切换到下个月
				$tgt_time = mktime(0, 0, 0, $_POST["month"] + 1, 1, $_POST["year"]);
			}
		}

		$mdays = date("t", $tgt_time); //当月总天数
		$datenow = date("j"); //当日日期
		$monthnow = date("n", $tgt_time); //当月月份
		$yearnow = date("Y", $tgt_time); //当年年份

		//计算当月第一天是星期几
		$wk1st = date("w", $tgt_time) - 1;
		$trnum = ceil(($mdays + $wk1st) / 7); //计算表格行数
		$out = "<tr class=cal-header><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th><th>日</th></tr>";
		for ($i = 0; $i < $trnum; $i++) {
			$out .= "<tr class=cal-body>";
			for ($k = 0; $k < 7; $k++) {
				//每行七个单元格
				$tabidx = $i * 7 + $k; //取得单元格自身序号
				//若单元格序号小于当月第一天的星期数($wk1st)或大于(月总数+$wk1st)
				//只填写空格，反之，写入日期
				($tabidx < $wk1st or $tabidx > $mdays + $wk1st - 1) ? $dayecho = "&nbsp" : $dayecho = $tabidx - $wk1st + 1;
				//突出标明今日日期
				// $dayecho="<span style=\"background-color:red;color:#fff;\">$dayecho</span>";
				$todayclass = "";
				if ($k > 4) {$todayclass .= " weekend";}
				if ($dayecho == $datenow
					&& $monthnow == date("n")
					&& $yearnow == date("Y")
				) {$todayclass .= " current";}
				$todaybg = empty($todayclass) ? "" : " class='$todayclass'";
				$out .= "<td" . $todaybg . ">$dayecho</td>";
			}
			$out .= "</tr>";
		}

		echo '{"success":true,"tbody":"' . $out . '", "year":' . $yearnow . ', "month":' . $monthnow . '}';
	}
}
?>