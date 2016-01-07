<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
	exit;
}

/**
 * 在侧边栏加入日历控件
 *
 * @package Calendar
 * @author BangZ
 * @version 1.0.0
 * @link http://typecho.org
 */
class Calendar_Plugin implements Typecho_Plugin_Interface {
	/**
	 * 激活插件方法,如果激活失败,直接抛出异常
	 *
	 * @access public
	 * @return void
	 * @throws Typecho_Plugin_Exception
	 */
	public static function activate() {
		//注入CSS
		Typecho_Plugin::factory('Widget_Archive')->header = array(__CLASS__, 'outputHeader');
		Typecho_Plugin::factory('Widget_Archive')->footer = array(__CLASS__, 'outputFooter');
		return _t('日历插件启动成功');
	}

	/**
	 * 禁用插件方法,如果禁用失败,直接抛出异常
	 *
	 * @static
	 * @access public
	 * @return void
	 * @throws Typecho_Plugin_Exception
	 */
	public static function deactivate() {}

	/**
	 * 获取插件配置面板
	 *
	 * @access public
	 * @param Typecho_Widget_Helper_Form $form 配置面板
	 * @return void
	 */
	public static function config(Typecho_Widget_Helper_Form $form) {
	}

	/**
	 * 个人用户的配置面板
	 *
	 * @access public
	 * @param Typecho_Widget_Helper_Form $form
	 * @return void
	 */
	public static function personalConfig(Typecho_Widget_Helper_Form $form) {}

	public static function buildCalendar() {
		//判断当前插件是否已被启用
		$options = Typecho_Widget::widget('Widget_Options');
		if (!isset($options->plugins['activated']['Calendar'])) {
			return '日历插件未被激活';
		}
		$mdays = date("t"); //当月总天数
		$datenow = date("j"); //当日日期
		$monthnow = date("n"); //当月月份
		$yearnow = date("Y"); //当年年份
		//计算当月第一天是星期几
		$wk1st = date("w", mktime(0, 0, 0, $monthnow, 1, $yearnow)) - 1;
		$trnum = ceil(($mdays + $wk1st) / 7); //计算表格行数
		$out = <<<HEAD
        <div id="cal_switcher" class="list-group-item clearfix">
        	<a href="javascript:move('left')"><i class="fa fa-angle-left"></i></a>
            <div id="cal_yearmonth">
	            <span id="cal_year"><b>{$yearnow}</b>年</span>
	            <span id="cal_month"><b>{$monthnow}</b>月</span>
            </div>
            <a href="javascript:move('right')"><i class="fa fa-angle-right"></i></a>
        </div>
HEAD;
		//以下是表格字串
		$out .= "<table id=cal_plugin class=\"table table-bordered\"><tr class=cal-header><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th><th>日</th></tr>";
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
				if ($dayecho == $datenow) {$todayclass .= " current";}
				$todaybg = empty($todayclass) ? "" : " class=\"$todayclass\"";
				$out .= "<td" . $todaybg . ">$dayecho</td>";
			}
			$out .= "</tr>";
		}
		$out .= "</table>" . PHP_EOL;
		return $out;
	}

	/**
	 * 插件实现方法
	 *
	 * @access public
	 * @return void
	 */
	public static function render() {
		echo Calendar_Plugin::buildCalendar();
		//echo '<a href="{url}" class="list-group-item" title="{title}" target="_blank">这里是日历插件</a>';
	}

	//嵌入CSS
	public static function outputHeader() {
		$cssUrl = Helper::options()->pluginUrl . '/Calendar/calendar.css';
		echo '<link rel="stylesheet" type="text/css" href="' . $cssUrl . '" />' . PHP_EOL;
	}

	//嵌入JS
	public static function outputFooter() {
		$jsUrl = Helper::options()->pluginUrl . '/Calendar/calendar.js';
		$phpUrl = Helper::options()->pluginUrl . '/Calendar/switchtable.php';
		echo "<script type=\"text/javascript\">var php_Url = \"{$phpUrl}\";</script>" . PHP_EOL;
		echo '<script type="text/javascript" src="' . $jsUrl . '" async defer></script>' . PHP_EOL;
		//思路：再定义一份ajax
	}
}
