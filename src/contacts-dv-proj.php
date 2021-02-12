<?php
$TRUE_EXTENSIONS = array(
    'jpg', 'jpeg', 'png', 'gif', 'bmp', 'doc', 'xls', 'ppt', 'pps', 'docm', 'docx', 'xlsx', 'xlsm', 'xlsb', 'pptx', 'pptm', 'ppsx', 'ppsm', 'pdf',
    'rar', 'zip', '7z', 'tar', 'tar.gz', 'gz', 'gzip', 'tar-gz'
);

if (isset ($_POST['email'])) {
    $to = $_POST['admin_email'];
    $from = 'no-reply@dv-project.ru ';
    $subject = $_POST['project_name'];
    $message = "Тема: ".$_POST['form_subject']."\nИмя: ".$_POST['name']."\nEmail: ".$_POST['email']."\nPhone: ".$_POST['phone']."\nIP: ".$_SERVER['REMOTE_ADDR'];
    $boundary = md5(date('r', time()));
    $filesize = '';
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: " . $from . "\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
    $message="
Content-Type: multipart/mixed; boundary=\"$boundary\"

--$boundary
Content-Type: text/plain; charset=\"utf-8\"
Content-Transfer-Encoding: 7bit

$message";

    $no_valid_files = array();

    for($i=0;$i<count($_FILES['fileFF']['name']);$i++) {
        if(is_uploaded_file($_FILES['fileFF']['tmp_name'][$i])) {
            $attachment = chunk_split(base64_encode(file_get_contents($_FILES['fileFF']['tmp_name'][$i])));
            $filename = $_FILES['fileFF']['name'][$i];
            $filetype = $_FILES['fileFF']['type'][$i];
            $filesize += $_FILES['fileFF']['size'][$i];

            if(!check_file_extension($filename)) $no_valid_files[] = $filename;

            $message.="

--$boundary
Content-Type: \"$filetype\"; name=\"$filename\"
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename=\"$filename\"

$attachment";
        }
    }
    $message.="
--$boundary--";

    if($_SERVER['REMOTE_ADDR'] == '94.41.207.162' && 1 == 2) {
        if($no_valid_files) {
            die('Извините, письмо не отправлено. <br>Невозможно загрузить некоторые файлы:<br> '.implode('<br>', $no_valid_files));
        }
    }

    if ($filesize < 10000000) {
        if($_SERVER['REMOTE_ADDR'] != '94.41.207.162') mail($to, $subject, $message, $headers);
    }else{
        die('Письмо не может быть отправлено, так как файл весит больше 10мб, вышлете его на почту aps@dv-p.ru');
    }
    crm_post();
    print('OK');
}


function crm_post() {
    $crm_request = $files = array();
    $cookie_replace = array(
        '_uc_utm_source' => '_uc_utm_source',
        '_uc_utm_medium' => '_uc_utm_medium',
        '_uc_utm_campaign' => '_uc_utm_campaign',
        '_uc_utm_term' => '_uc_utm_term',
        '_uc_utm_content' => '_uc_utm_content',
        'roistat_visit' => 'roistat_visit',
    );
    $request_replace = array(
        'name' => 'name',
        'phone' => 'phone',
        'email' => 'email',
        'form_subject' => 'form_name',
    );
    foreach ($cookie_replace as $k=>$v) $crm_request[$v] = $_COOKIE[$k];
    foreach ($request_replace as $k=>$v) $crm_request[$v] = ($_POST[$k] ? $_POST[$k] : $_REQUEST[$k]);
    for($i=0;$i<count($_FILES['fileFF']['name']);$i++) {
        if(!is_uploaded_file($_FILES['fileFF']['tmp_name'][$i])) continue;
        $files['files['.$i.']'] = curl_file_create($_FILES['fileFF']['tmp_name'][$i], $_FILES['fileFF']['type'][$i],  $_FILES['fileFF']['name'][$i]);
    }
    $crm_request['host'] = $_SERVER['HTTP_REFERER'];

    $data = $crm_request + $files;
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_URL => "https://t24platform.ru/integrations/dv_project_PGcArQ/site/tsend.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLINFO_HEADER_OUT => true,
        CURLOPT_POSTFIELDS => $data
    ));
    $result = curl_exec($ch);
    curl_close ($ch);
}

function check_file_extension($file) {
    global $TRUE_EXTENSIONS;
    return in_array(substr(strrchr($file, "."), 1), $TRUE_EXTENSIONS);
}

?>