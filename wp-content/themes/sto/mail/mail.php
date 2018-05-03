<?php 

/***SEND MESSAGE***/
$mailto = 'remont.gruzowikov@yandex.by';
$mailfrom = isset($_POST['email']) ? $_POST['email'] : $mailto;
$mailcc = 'info@amount.by';
$subject = 'ремонт-грузовиков.бел';
$message = '';
$eol = "\n";
$boundary = md5(uniqid(time()));

$header  = 'From: '.$mailfrom.$eol;
$header .= 'Reply-To: '.$mailfrom.$eol;
$header .= 'Cc: '.$mailcc.$eol;
$header .= 'MIME-Version: 1.0'.$eol;
$header .= 'Content-Type: multipart/mixed; boundary="'.$boundary.'"'.$eol;
$header .= 'X-Mailer: PHP v'.phpversion().$eol;

$message .= $eol;
$message .= "IP Address : " . $_SERVER['REMOTE_ADDR'];
$message .= $eol;

$internalfields = array ("submit", "reset", "send", "filesize", "formid", "captcha_code", "recaptcha_challenge_field", "recaptcha_response_field", "g-recaptcha-response");

foreach ($_POST as $key => $value) {
    if (!in_array(strtolower($key), $internalfields)) {
        if (!is_array($value)) {
            $message .= ucwords(str_replace("_", " ", $key)) . " : " . $value . $eol;
        } else {
            $message .= ucwords(str_replace("_", " ", $key)) . " : " . implode(",", $value) . $eol;
        }
    }
}

$body  = 'This is a multi-part message in MIME format.'.$eol.$eol;
$body .= '--'.$boundary.$eol;
$body .= 'Content-Type: text/plain; charset=UTF-8'.$eol;
$body .= 'Content-Transfer-Encoding: 8bit'.$eol;
$body .= $eol.stripslashes($message).$eol;

if (!empty($_FILES)) {
    foreach ($_FILES as $key => $value) {
         if ($_FILES[$key]['error'] == 0 && $_FILES[$key]['size'] <= $max_filesize) {
                $body .= '--'.$boundary.$eol;
                $body .= 'Content-Type: '.$_FILES[$key]['type'].'; name='.$_FILES[$key]['name'].$eol;
                $body .= 'Content-Transfer-Encoding: base64'.$eol;
                $body .= 'Content-Disposition: attachment; filename='.$_FILES[$key]['name'].$eol;
                $body .= $eol.chunk_split(base64_encode(file_get_contents($_FILES[$key]['tmp_name']))).$eol;
         }
    }
}

$body .= '--'.$boundary.'--'.$eol;

if ($mailto != '') {
    mail($mailto, $subject, $body, $header);
        
    if (!empty($_POST) && $_POST['форма'] != 'Сотрудничество' && $_POST['форма'] != 'Коммерческое предложение'){
  /***SEND TO CRM***/  
      $user=array(
      'USER_LOGIN'=>'remont.gruzowikov@yandex.ru', #Ваш логин (электронная почта)
     'USER_HASH'=>'b9029b58d508f0b7671e6fe8af79a127' #Хэш для доступа к API (смотрите в профиле пользователя)
    );
    $subdomain='remontgruzovikov'; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($user));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
    curl_close($curl); #Завершаем сеанс cURL
    /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
     if($code!=200 && $code!=204)
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
    }
    catch(Exception $E)
    {
      die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
    }
    /*
     Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);
    $Response=$Response['response'];
    if(isset($Response['auth'])){
        
    $contacts['add']=array(
       array(
          'name' => 'ремонт-грузовиков.бел',
          'responsible_user_id' => 1762213,
          'created_by' => 1762213,
          'created_at' => date('U'),
          'custom_fields' => array(
             array(
                'id' => 293149,
                'values' => array(
                   array(
                      'value' => $_POST['телефон'],
                      'enum' => "WORK"
                   ),
                )
             ),
            array(
                'id' => 293151,
                'values' => array(
                   array(
                      'value' => isset($_POST['email'])? $_POST['email'] : '',
                      'enum' => "WORK"
                   ),
                )
             ),
    
          )
       )
    );
    /* Теперь подготовим данные, необходимые для запроса к серверу */
    $subdomain='remontgruzovikov'; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/api/v2/contacts';
    /* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
    работе с этой
    библиотекой Вы можете прочитать в мануале. */
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($contacts));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
     if($code!=200 && $code!=204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
      }
    }
    catch(Exception $E)
    {
      die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
    }
    /*
     Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);
    $Response=$Response['_embedded']['items'];
    foreach($Response as $v)
     if(is_array($v))
       $contact_id = $v['id'];
    
    $leads['add']=array(
      array(
        'name'=>'Заявка с сайта ремонт-грузовиков.бел',
        'created_at'=>date('U'),
        'status_id'=>16511737,
        'sale'=>0,
        'tags' => 'ремонт-грузовиков.бел', #Теги
        'contacts_id' =>array($contact_id),
       'custom_fields'=>array(
           array(
            'id'=>418643, #название формы
           'values'=>array(
              array(
                'value'=>isset($_POST['форма'])? $_POST['форма'] : ''
              )
            )
          ),
          array(
            'id'=>418425, #Марка
           'values'=>array(
              array(
                'value'=>isset($_POST['марка'])? $_POST['марка'] : ''
              )
            )
          ),
          array(
            'id'=>418429, #Проблема
           'values'=>array(
              array(
                'value'=>isset($_POST['проблема'])? $_POST['проблема'] : ''
              )
            )
          ),
          array(
            'id'=>293621, #Комментарии
           'values'=>array(
              array(
                'value'=>isset($_POST['комментарий'])? $_POST['комментарий'] : ''
              )
            )
          ),
          array(
            'id'=>418431, #Способ связи
           'values'=>array(
              array(
                'value'=>isset($_POST['способ_связи'])? $_POST['способ_связи'] : ''
              )
            )
          ),
          array(
            'id'=>418203, #utm_source
           'values'=>array(
              array(
                'value'=>isset($_POST['utm_source'])? $_POST['utm_source'] : ''
              )
            )
          ),
           array(
            'id'=>418215, #utm_medium
           'values'=>array(
              array(
                'value'=>isset($_POST['utm_medium'])? $_POST['utm_medium'] : ''
              )
            )
          ),
          array(
            'id'=>418447, #utm_campaign
           'values'=>array(
              array(
                'value'=>isset($_POST['utm_campaign'])? $_POST['utm_campaign'] : ''
              )
            )
          ),
          array(
            'id'=>418433, #utm_term
           'values'=>array(
              array(
                'value'=>isset($_POST['utm_term'])? $_POST['utm_term'] : ''
              )
            )
          ),
          array(
            'id'=>418437, #utm_content
           'values'=>array(
              array(
                'value'=>isset($_POST['utm_content'])? $_POST['utm_content'] : ''
              )
            )
          ),
          array(
            'id'=>418443, #page ref
           'values'=>array(
              array(
                'value'=>isset($_POST['переход'])? $_POST['переход'] : ''
              )
            )
          ),
            array(
            'id'=>421969, #key
           'values'=>array(
              array(
                'value'=>isset($_POST['key'])? $_POST['key'] : ''
              )
            )
          ),
           array(
            'id'=>421971, #pos
           'values'=>array(
              array(
                'value'=>isset($_POST['yd_position'])? $_POST['yd_position'] : ''
              )
            )
          ),
           array(
            'id'=>421973, #block
           'values'=>array(
              array(
                'value'=>isset($_POST['block'])? $_POST['block'] : ''
              )
            )
          ),
           array(
            'id'=>421975, #type
           'values'=>array(
              array(
                'value'=>isset($_POST['yd_position_type'])? $_POST['yd_position_type'] : ''
              )
            )
          ),
           array(
            'id'=>421977, #region_name
           'values'=>array(
              array(
                'value'=>isset($_POST['region_name'])? $_POST['region_name'] : ''
              )
            )
          ),
           array(
            'id'=>421979, #ad
           'values'=>array(
              array(
                'value'=>isset($_POST['ad'])? $_POST['ad'] : ''
              )
            )
          ),
           array(
            'id'=>421981, #added
           'values'=>array(
              array(
                'value'=>isset($_POST['added'])? $_POST['added'] : ''
              )
            )
          ),
           array(
            'id'=>421983, #addphrasestext
           'values'=>array(
              array(
                'value'=>isset($_POST['yd_addphrasestext'])? $_POST['yd_addphrasestext'] : ''
              )
            )
          ),
           array(
            'id'=>421985, #campaign_type
           'values'=>array(
              array(
                'value'=>isset($_POST['campaign_type'])? $_POST['campaign_type'] : ''
              )
            )
          ),
           array(
            'id'=>421987, #campaign
           'values'=>array(
              array(
                'value'=>isset($_POST['campaign'])? $_POST['campaign'] : ''
              )
            )
          ),
           array(
            'id'=>421989, #device
           'values'=>array(
              array(
                'value'=>isset($_POST['yd_device_type'])? $_POST['yd_device_type'] : ''
              )
            )
          ),
           array(
            'id'=>421991, #gbid
           'values'=>array(
              array(
                'value'=>isset($_POST['gbid'])? $_POST['gbid'] : ''
              )
            )
          ),
           array(
            'id'=>421993, #phrase
           'values'=>array(
              array(
                'value'=>isset($_POST['phrase'])? $_POST['phrase'] : ''
              )
            )
          ),
           array(
            'id'=>421995, #retargeting
           'values'=>array(
              array(
                'value'=>isset($_POST['retargeting'])? $_POST['retargeting'] : ''
              )
            )
          ),
           array(
            'id'=>421997, #adtarget_name
           'values'=>array(
              array(
                'value'=>isset($_POST['yd_adtarget_name'])? $_POST['yd_adtarget_name'] : ''
              )
            )
          ),
           array(
            'id'=>421999, #adtarget_id
           'values'=>array(
              array(
                'value'=>isset($_POST['adtarget_id'])? $_POST['adtarget_id'] : ''
              )
            )
          ),
           array(
            'id'=>422001, #region
           'values'=>array(
              array(
                'value'=>isset($_POST['region'])? $_POST['region'] : ''
              )
            )
          ),
           array(
            'id'=>422177, #yd_source
           'values'=>array(
              array(
                'value'=>isset($_POST['yd_source'])? $_POST['yd_source'] : ''
              )
            )
          ),
           array(
            'id'=>422179, #yd_source_type
           'values'=>array(
              array(
                'value'=>isset($_POST['yd_source_type'])? $_POST['yd_source_type'] : ''
              )
            )
          ),
           
        )
      ),
    );
    /* Теперь подготовим данные, необходимые для запроса к серверу */
    $subdomain='remontgruzovikov'; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/api/v2/leads';
    /* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
    работе с этой
    библиотекой Вы можете прочитать в мануале. */
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
     if($code!=200 && $code!=204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
      }
    }
    catch(Exception $E)
    {
      die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
    }
    
    } else {
    echo 'Авторизация не удалась';
    }  
        }
}
    

