<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/* Escape (for safe HTML output) */
function h($str){
  return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/* CSRF */
function ensure_csrf_token() {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
}
function csrf_field() {
  ensure_csrf_token();
  return '<input type="hidden" name="csrf" value="'.h($_SESSION['csrf_token']).'">';
}
function require_post_csrf() {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') return false;
  if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf'])) {
    http_response_code(403);
    die("Invalid CSRF token.");
  }
  return true;
}

/* Flash */
function flash($key, $msg=null, $type='success'){
  if ($msg !== null){ 
    $_SESSION['flash'][$key] = ['msg'=>$msg,'type'=>$type]; 
    return; 
  }
  if (!empty($_SESSION['flash'][$key])) {
    $f = $_SESSION['flash'][$key]; 
    unset($_SESSION['flash'][$key]);
    $cls = $f['type']==='danger' ? 'danger' : ($f['type']==='warning' ? 'warning' : 'success');
    return '<div class="alert alert-'.$cls.' alert-dismissible fade show" role="alert">'
      .h($f['msg'])
      .'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
  }
  return '';
}

/* Validation helpers (basic) */
function v_required($v){ return trim((string)$v) !== ''; }
function v_email($v){ return filter_var($v, FILTER_VALIDATE_EMAIL); }
function v_len($v,$min=0,$max=255){ 
  $l=mb_strlen((string)$v); 
  return $l >= $min && $l <= $max; 
}
function v_number($v,$min=null,$max=null){ 
  if(!is_numeric($v)) return false; 
  $n = $v+0; 
  if($min!==null && $n<$min) return false; 
  if($max!==null && $n>$max) return false; 
  return true; 
}

/* Pagination */
function paginate($total, $page, $per_page){
  $pages = max(1, (int)ceil($total/$per_page));
  $page = max(1, min($page, $pages));
  $offset = ($page-1)*$per_page;
  return [$page,$pages,$offset];
}
function render_pager($page,$pages){
  if ($pages<=1) return '';
  $q=$_GET; 
  $html='<nav><ul class="pagination pagination-sm justify-content-end mt-3">';
  $prev = max(1,$page-1); 
  $next = min($pages,$page+1);

  $q['page']=$prev; 
  $html.='<li class="page-item '.($page==1?'disabled':'').'">
            <a class="page-link" href="?'.http_build_query($q).'">&laquo;</a>
          </li>';

  for($i=max(1,$page-2); $i<=min($pages,$page+2); $i++){ 
    $q['page']=$i; 
    $html.='<li class="page-item '.($i==$page?'active':'').'">
              <a class="page-link" href="?'.http_build_query($q).'">'.$i.'</a>
            </li>'; 
  }

  $q['page']=$next; 
  $html.='<li class="page-item '.($page==$pages?'disabled':'').'">
            <a class="page-link" href="?'.http_build_query($q).'">&raquo;</a>
          </li>';

  $html.='</ul></nav>';
  return $html;
}
