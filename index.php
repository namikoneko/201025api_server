<?php
require_once 'idiorm.php';
ORM::configure('sqlite:./data.db');
ORM::configure('return_result_sets', true);
require 'flight/Flight.php';

function json_echo($list){
    header("Content-Type: application/json; charset=utf-8");
    $arr = Flight::json($list);
    echo $arr;
}

function make_list_one($row){
    $list["id"] = $row["id"];
    $list["title"] = $row["title"];
    $list["text"] = $row["text"];
    return $list;
}

function make_list_many($rows){
    $i = 0;
    foreach($rows as $row){
        $list[$i]["id"] = $row["id"];
        $list[$i]["title"] = $row["title"];
        $list[$i]["text"] = $row["text"];
        $i++;
    }
    return $list;
}

// users ##################################################
Flight::route('/users', function(){
    $rows = ORM::for_table('user')->order_by_desc('updated')->find_many();
    json_echo(make_list_many($rows));
});

// user_ins ##################################################
Flight::route('/userins', function(){
    $row = ORM::for_table('user')->create();
    $row->title = Flight::request()->data->title;
    $row->updated = time();
    $row->save();
    Flight::redirect('/users');
});

// user ##################################################
Flight::route('/user/@userid', function($userid){
    $row = ORM::for_table('user')->find_one($userid);
    json_echo(make_list_one($row));
});

// cls ##################################################
Flight::route('/cls/@userid', function($userid){
    $rows = ORM::for_table('cl')->where('userid',$userid)->order_by_desc('updated')->find_many();
    json_echo(make_list_many($rows));
});

// cl_ins ##################################################
Flight::route('/clins', function(){
    $row = ORM::for_table('cl')->create();
    $userid = Flight::request()->data->userid;
    $row->userid = $userid; 
    $row->title = Flight::request()->data->title;
    $row->updated = time();
    $row->save();
    Flight::redirect('/cls/' . $userid);
});

// cl_up ##################################################
Flight::route('/clup/@id', function($id){
    $row = ORM::for_table('cl')->find_one($id);
    $row->updated = time();
    $row->save();
});

// cl ##################################################
Flight::route('/cl/@clid', function($clid){
    $row = ORM::for_table('cl')->find_one($clid);
    json_echo(make_list_one($row));
//    $i = 0;
//    foreach($rows as $row){
//        $list[$i]["id"] = $row["id"];
//        $list[$i]["title"] = $row["title"];
//        $i++;
//    }
//    header("Content-Type: application/json; charset=utf-8");
//    echo Flight::json($list);
});

// posts ##################################################
Flight::route('/posts/@clid', function($clid){
    $rows = ORM::for_table('post')->where('clid',$clid)->order_by_desc('updated')->find_many();
    json_echo(make_list_many($rows));
});

// post_ins ##################################################
Flight::route('/postins', function(){
    $row = ORM::for_table('post')->create();
    $clid = Flight::request()->data->clid;
    $row->clid = $clid; 
    $row->title = Flight::request()->data->title;
    $row->updated = time();
    $row->save();
    Flight::redirect('/posts/' . $clid);
});

// post_up ##################################################
Flight::route('/postup/@id', function($id){
    $row = ORM::for_table('post')->find_one($id);
    $row->updated = time();
    $row->save();
});

// post ##################################################
Flight::route('/post/@postid', function($postid){
    $row = ORM::for_table('post')->find_one($postid);
    json_echo(make_list_one($row));
});

// threads ##################################################
Flight::route('/threads/@postid', function($postid){
    $count = ORM::for_table('thread')->where('postid',$postid)->where_like('title',"%" . "0000" . "%")->count();//find_many();
    if($count == 0){
      $rows = ORM::for_table('thread')->where('postid',$postid)->order_by_desc('updated')->find_many();
    }else{
      $rows = ORM::for_table('thread')->where('postid',$postid)->order_by_desc('title')->find_many();
    }
    json_echo(make_list_many($rows));
});

// thread_ins ##################################################
Flight::route('/threadins', function(){
    $row = ORM::for_table('thread')->create();
    $postid = Flight::request()->data->postid;
    $row->postid = $postid; 
    $row->title = Flight::request()->data->title;
    $row->text = Flight::request()->data->text;
    $row->updated = time();
    $row->save();
    Flight::redirect('/threads/' . $postid);
});

// thread_up ##################################################
Flight::route('/threadup/@id', function($id){
    $row = ORM::for_table('thread')->find_one($id);
    $row->updated = time();
    $row->save();
});

// thread_upd ##################################################
Flight::route('/threadupd/@id', function($id){
    $row = ORM::for_table('thread')->find_one($id);
    json_echo(make_list_one($row));
//    $list['title'] = $row->title;
//    $list['text'] = $row->text;
//    json_echo($list);
//    header("Content-Type: application/json; charset=utf-8");
//    $arr = Flight::json($list);
//    echo $arr;
});

// thread_updexe ##################################################
Flight::route('/threadupdexe', function(){
	$row = ORM::for_table('thread')->find_one(Flight::request()->data->id);
	$row->title = Flight::request()->data->title;
	$text = Flight::request()->data->text;
//	if(!is_null($text)){
	  $row->text = Flight::request()->data->text;
//        }
	$row->save();
});

// thread_del ##################################################
Flight::route('/threaddel/@id', function($id){
    $row = ORM::for_table('thread')->find_one($id);
    $row->delete();
});

// cl_upd ##################################################
Flight::route('/clupd/@id', function($id){
    $row = ORM::for_table('cl')->find_one($id);
    json_echo(make_list_one($row));
});

// cl_updexe ##################################################
Flight::route('/clupdexe', function(){
	$row = ORM::for_table('cl')->find_one(Flight::request()->data->id);
	$row->title = Flight::request()->data->title;
	$row->save();
});

// cl_del ##################################################
Flight::route('/cldel/@id', function($id){
    $row = ORM::for_table('cl')->find_one($id);
    $row->delete();
});

// post_upd ##################################################
Flight::route('/postupd/@id', function($id){
    $row = ORM::for_table('post')->find_one($id);
    json_echo(make_list_one($row));
});

// post_updexe ##################################################
Flight::route('/postupdexe', function(){
	$row = ORM::for_table('post')->find_one(Flight::request()->data->id);
	$row->title = Flight::request()->data->title;
	$row->save();
});

// post_del ##################################################
Flight::route('/postdel/@id', function($id){
    $row = ORM::for_table('post')->find_one($id);
    $row->delete();
});

// user_upd ##################################################
Flight::route('/userupd/@id', function($id){
    $row = ORM::for_table('user')->find_one($id);
    json_echo(make_list_one($row));
});

// user_updexe ##################################################
Flight::route('/userupdexe', function(){
	$row = ORM::for_table('user')->find_one(Flight::request()->data->id);
	$row->title = Flight::request()->data->title;
	$row->save();
});

// user_del ##################################################
Flight::route('/userdel/@id', function($id){
    $row = ORM::for_table('user')->find_one($id);
    $row->delete();
});
// ins_exe ##################################################
Flight::route('/ins_exe', function(){
	$result = ORM::for_table('test')->create();
	$result->date = date('Y-m-d');
	$result->title = Flight::request()->query->title;
	$result->text = Flight::request()->query->text;
	$result->updated = time();
	$result->save();
	//Flight::redirect('/');
	Flight::redirect('/?title=' . Flight::request()->query->title);
});

// upd ##################################################
Flight::route('/upd', function(){
	$results = ORM::for_table('test')->find_one(Flight::request()->query->id);
	//foreach($results as $result){
	$str = "";
	//$str .= "<form action='index.php?func=upd_exe' method='post'>";
	$str .= "<form action='upd_exe' method='post'>";
	$str .= "<input type='hidden' name='id' value=" . Flight::request()->query->id . ">";

	if(isset(Flight::request()->query->page)){
		$str .= "<input type='hidden' name='page' value=" . Flight::request()->query->page . ">";
	}

	$str .= "<input type='text' name='date' value='";
	$str .= $results->date;
	$str .= "'><br>";
	$str .= "<input type='text' name='title' value='";
	$str .= $results->title;
	$str .= "'><br>";
	$str .= "<input type='text' name='text' value='";
	$str .= $results->text;
	$str .= "'><br>";
	$str .= "<input type='submit' value='send'>";
	$str .= "</form>";
	Flight::render('header', array('heading' => 'Hello'), 'header_content');
	Flight::render('body', array('str' => $str), 'body_content');
	Flight::render('layout', array('title' => 'todo'));
});

// upd_exe ##################################################
Flight::route('/upd_exe', function(){
	$results = ORM::for_table('test')->find_one(Flight::request()->data->id);
	$results->date = Flight::request()->data->date;
	$results->title = Flight::request()->data->title;
	$results->text = Flight::request()->data->text;
	$results->save();
	if(isset(Flight::request()->data->page)){
		Flight::redirect('/?page=' . Flight::request()->data->page . '&title=' . Flight::request()->data->title);
	}else{
		//Flight::redirect('/');
		Flight::redirect('/?title=' . Flight::request()->data->title);
	}
});

// del ##################################################
Flight::route('/del', function(){
	$results = ORM::for_table('test')->find_one(Flight::request()->query->id);
	$results->delete();
	//Flight::redirect('/');
	Flight::redirect('/?title=' . Flight::request()->query->title);
});

// up ##################################################
Flight::route('/up', function(){
	//$results = ORM::for_table('test')->find_one(Flight::request()->query->id);
	if(isset(Flight::request()->query->title)){
		$results = ORM::for_table('test')->where_like('title',"%" . Flight::request()->query->title . "%")->order_by_desc('updated')->find_many();
	}else if(isset(Flight::request()->query->q_all)){
		$results = ORM::for_table('test')->where_raw('("title" like ? or "text" like ?)',array("%" . $q_all . "%","%" . $q_all . "%"))->order_by_desc('updated')->find_many();
	}else{
		$results = ORM::for_table('test')->order_by_desc('updated')->find_many();
	}

	$i = 0;
	foreach($results as $result){
		$ids[] = $result->id;
		// 配列の何番目か調べる
		if(Flight::request()->query->id == $result->id){
			$myId_i = $i;
		}
		$i++;
	}

	// 1番目のレコードでなければ
	if($myId_i != 0){
		$results[$myId_i]->updated = ORM::for_table('test')->find_one($ids[($myId_i - 1)])->updated + 1;
	}
	$results->save();
	//Flight::redirect('');
	Flight::redirect('?title=' . Flight::request()->query->title);
});

// down ##################################################
Flight::route('/down', function(){
	if(isset(Flight::request()->query->title)){
		$results = ORM::for_table('test')->where_like('title',"%" . Flight::request()->query->title . "%")->order_by_desc('updated')->find_many();
	}else if(isset(Flight::request()->query->q_all)){
		$results = ORM::for_table('test')->where_raw('("title" like ? or "text" like ?)',array("%" . $q_all . "%","%" . $q_all . "%"))->order_by_desc('updated')->find_many();
	}else{
		$results = ORM::for_table('test')->order_by_desc('updated')->find_many();
	}

	$i = 0;
	foreach($results as $result){
		$ids[] = $result->id;
		// 配列の何番目か調べる
		if(Flight::request()->query->id == $result->id){
			$myId_i = $i;
		}
		$i++;
	}

	// 最後のレコードでなければ
	$records = ORM::for_table('test')->count();
	if($myId_i != $records){
		$results[$myId_i]->updated = ORM::for_table('test')->find_one($ids[($myId_i + 1)])->updated - 1;
	}
	$results->save();
	Flight::redirect('?title=' . Flight::request()->query->title);
});

// distinct ##################################################
Flight::route('/dist', function(){
	//$results = ORM::for_table('test')->distinct()->select('title')->order_by_desc('date')->find_many();
	$results = ORM::for_table('test')->order_by_desc('date')->find_many();

	//$titleUniques[] = "";
	foreach($results as $result){
		$titleUniques[] = $result->title;
	}
		$titleUniques = array_unique($titleUniques);

	$str = "";

	if(!empty(Flight::request()->query->title)){
		//単純検索title
		//$results = $results->where_like('title',"%" . Flight::request()->query->title . "%")->find_many();
		$results = ORM::for_table('test')->where_like('title',"%" . Flight::request()->query->title . "%")->distinct()->select('title')->order_by_asc('title')->find_many();
	}
	
	$str .=<<<EOD
	<a class='button' href='dist'>dist_all</a>
	<a class='button' href='list'>list</a>

	<form action='dist' method='get'>
		<input type='text' name='title'>
		<input type='submit' value='send'>
	</form>

	<table>
		<thead>
			<tr>
				<th>title</th>
			</tr>
		</thead>
		<tbody>
EOD;
	foreach($titleUniques as $titleUnique){
		$str .= "<tr>";
		$str .= "<td>";
		$str .= $titleUnique;
		$str .= "</td><td>";

		//$record = ORM::for_table('test')->where('title',$titleUnique)->order_by_desc('date')->find_one();
		//$str . = $record->date; 

		//$str .= "</td><td>";
		$str .= "<a href='list?title=" . $titleUnique . "'>list</a>";
		$str .= "</td>";
		$str .= "</tr>";
	}

	$str .=<<<EOD
	</tbody>
	</table>
EOD;

	Flight::render('header', array('heading' => 'Hello'), 'header_content');
	Flight::render('body', array('str' => $str), 'body_content');
	Flight::render('layout', array('title' => 'todo'));

});

// select ##################################################
Flight::route('/*', function(){
	//echo "<a href='index.php?func=ins'>insert</a><br>";
	$str = "";
	//$str .= "<a href='ins'>insert</a><br>";

	// ページング
	if(isset(Flight::request()->query->page)){
		$page = Flight::request()->query->page;
	}else{
		$page = 1;
	}

	$records = ORM::for_table('test')->count();
	$per_page = 15;
	$offset = ($page - 1) * $per_page;

	// クエリ
	//$results = ORM::for_table('test')->find_many();
	//$q_single = Flight::request()->query->q_single;
	$title = Flight::request()->query->title;
	$q_all = Flight::request()->query->q_all;
	if(!empty($title)){
		//単純検索title
		$results = ORM::for_table('test')->where_like('title',"%" . Flight::request()->query->title . "%")->limit($per_page)->offset($offset)->order_by_desc('updated')->find_many();
	//}else if(!empty($title)){
	}else if(!empty($q_all)){
	//single入力w検索
		$results = ORM::for_table('test')->where_raw('("title" like ? or "text" like ?)',array("%" . $q_all . "%","%" . $q_all . "%"))->limit($per_page)->offset($offset)->order_by_desc('updated')->find_many();
	}else if(!empty(Flight::request()->query->distinct)){
		$results = ORM::for_table('test')->where_like('title',"%" . Flight::request()->query->distinct . "%")->limit($per_page)->offset($offset)->order_by_desc('updated')->distinct()->select('title')->find_many();
	}else{
		//全表示
		$results = ORM::for_table('test')->limit($per_page)->offset($offset)->order_by_desc('updated')->find_many();
	}
	//w入力w検索
	//$results = ORM::for_table('test')->where('archive',0)->where_raw('("title" like ? or "text" like ?)',array("%" . Flight::request()->query->title . "%","%" . Flight::request()->query->q_text . "%"))->limit($per_page)->offset($offset)->order_by_desc('updated')->find_many();

	$str .=<<<EOD
	<div class='row'>
		<div class="three columns">
		<form action='ins_exe?title='
EOD;
		if(isset(Flight::request()->query->title)){
			Flight::request()->query->title;
		}

	$str .=<<<EOD
		' method='get'>
					<input type='text' name='title' value='
EOD;
		if(isset($title)){
			$str .= $title;
		}
					
	$str .=<<<EOD
'><br>
					<input type='text' name='text'>
					<!--
					<textarea name='text' cols=30 rows=10></textarea>
					<br>
					-->
					<input type='submit' value='send'>
		</form>
		</div>

		<div class="three columns">
			<form action='' method='get'>
				<input type='text' name='title'>
				<input type='submit' value='title'>
			<a class='button' href='select'>list_all</a>
			</form>
		</div>
		<div class="three columns">
			<form action='' method='get'>
				<input type='text' name='q_all'>
				<input type='submit' value='all'>
			<a class='button' href='dist'>dist</a>
			</form>
		</div>
		<div class="three columns">
			<form action='' method='get'>
				<input type='text' name='distinct'>
				<input type='submit' value='distinct'>
			</form>
		</div>
	</div>
	<table>
		<thead>
			<tr>
				<th>id</th>
				<th>date</th>
				<th>title</th>
				<th>text</th>
				<th>updated</th>
				<th>up</th>
				<th>down</th>
				<th>update</th>
				<th>delete</th>
			</tr>
		</thead>
		<tbody>
EOD;
	foreach($results as $result){
		$str .= "<tr>";
		$str .= "<td>";
		$str .= $result->id;
		$str .= "</td><td>";
		$str .= $result->date;
		$str .= "</td><td>";
		/*
		$str .= "</td><td class='titleLink'>";
		*/
		$str .= $result->title;
		$str .= "</td><td>";
		$str .= $result->text;
		$str .= "</td><td>";
		$str .= $result->updated;
		$str .= "</td><td>";
		//$str .= nl2br($result->text,false);

		/*
		// 対象文字列
		$text = nl2br($result->text,false);
		// パターン
		$pattern = '/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/u';
		// 置換後の文字列
		$replacement = '<a href="\1">\1</a>';
		// 置換
		$str .= preg_replace($pattern,$replacement,$text);

		$str .= "</td><td>";
		//$str .= $result->updated;
		//$str .= "</td><td>";
		//$str .= $result->archive;
		//$str .= "</td><td>";
		*/
		$str .= "<a href='up?id=" . $result->id . "&title=" . Flight::request()->query->title . "'>up</a>";
		$str .= "</td><td>";
		$str .= "<a href='down?id=" . $result->id . "&title=" . Flight::request()->query->title . "'>down</a>";
		$str .= "</td><td>";
		if(isset(Flight::request()->query->page)){
			$str .= "<a href='upd?id=" . $result->id . "&page=" . Flight::request()->query->page . "'>update</a>";
		}else{
			$str .= "<a href='upd?id=" . $result->id . "'>update</a>";
		}
		/*
		$str .= "</td><td>";
		$str .= "<a href='arc_exe?id=" . $result->id . "'>kzm</a>";
		*/
		$str .= "</td><td>";
		$str .= "<a href='del?id=" . $result->id . "&title=" . Flight::request()->query->title . "'>delete</a>";
		$str .= "</td>";
		$str .= "</tr>";
	}

	$str .=<<<EOD
	</tbody>
	</table>
EOD;

	// ページング
	if($page > 1){
		$str .= "<a class='button' href='?page=" . ($page - 1) . "'>previous</a>";
	}
	if($page < ceil($records/$per_page)){
		$str .= "<a class='button' href='?page=" . ($page + 1) . "'>after</a>";
	}
	//echo $str;
	//Flight::render('result.php', array('str' => $str));


	Flight::render('header', array('heading' => 'Hello'), 'header_content');
	Flight::render('body', array('str' => $str), 'body_content');
	Flight::render('layout', array('title' => 'todo'));

//}
});
//if(isset($_GET['func'])){//仮
//if($_GET['func'] == "upd"){//仮
Flight::start();
