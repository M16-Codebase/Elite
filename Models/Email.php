<?php
/**
 * Description of Email
 *
 * @author olga
 */
namespace Models;
class Email {
    const LOG_TABLE = 'email_logs';
    const FIELD_NAME = 'name';
    const FIELD_EMAIL = 'email';
    const FIELD_TYPE = 'type';
    const FIELD_PHONE = 'phone';
    const FIELD_NUMBER = 'phone';
    private static $main_fields = array(self::FIELD_NAME, self::FIELD_NUMBER, self::FIELD_EMAIL, self::FIELD_TYPE, self::FIELD_PHONE);
    /**
	 * Email sender
	 *
	 * @param \LPS\Container\WebContentContainer $contentObj
	 * @param array $to ["email" => "name"]
	 * @param string $from
     * @param string $fromName
     * @param array  $attaches array('' => array(file_name => '', url => '') ...)
     * @param bool $localTest не отправляет письмо, а выводит содержимое в шаблон письма
     * @param array $failures список ошибок
     * @param bool $notSendToDevelopers не отправлять нам
	 * @return bool
	 */
	static function send(\LPS\Container\WebContentContainer $contentObj, array $to, $from = null, $fromName = null, array $attaches = array(), $localTest = false, &$failures = NULL, $notSendToDevelopers = NULL){
        if (!\LPS\Config::isRelease()){
            $to = \LPS\Config::getParametr('email', 'to');
        }
        //todo: выбрать оптимальный тип транспорта
        if (\LPS\Config::isLocal()){
            // SMTP
            $transport = \Swift_SmtpTransport::newInstance(
                \LPS\Config::getParametr('email', 'smtp_account_connect_host'), 
                \LPS\Config::getParametr('email', 'smtp_account_connect_port'), 
                \LPS\Config::getParametr('email', 'smtp_account_connect_security'))
                ->setUsername(\LPS\Config::getParametr('email', 'smtp_account_login'))
                ->setPassword(\LPS\Config::getParametr('email', 'smtp_account_pass'))
            ;
        }else{
            // Mails
            $transport = \Swift_MailTransport::newInstance();
        }

        // Перекрываем email`u
		//$to = array('compatibletest@mail.ru');

        // Sendmail
//        $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
        $mailer = \Swift_Mailer::newInstance($transport);

        $config = \App\Builder::getInstance()->getConfig();
        \Swift_Preferences::getInstance()->setCharset($config::getParametr('site', 'charset'));

        $subject = '';
        if (empty($from)){
            $from = $config::getParametr('email', 'from');
        }
        if (empty($fromName)){
            $fromName = $config::getParametr('email', 'support_name');
        }
        $contentObj->addRef('subject', $subject);
        $contentObj->add('site_url', preg_replace('~^(http:\/\/)?(www\.)?~', '', $config::getParametr('site', 'url')));
        $contentObj->add('from', $from);
        $contentObj->add('infoBlocks', \App\Blocks::getInstance());
        $contentObj->add('site_config', \App\Builder::getInstance()->getSiteConfig());
        $contentObj->add('site_title', \LPS\Config::getParametr('site', 'title'));
        $contentObj->add('fromName', $fromName);
        $mailContent = $contentObj->getContent();//внутренняя часть письма
        $mailHTMLObj = new \LPS\Container\WebContentContainer('mails/html.tpl');
        $mailHTMLObj->add('subject', $subject);
        $mailHTMLObj->add('from', $from);
        $mailHTMLObj->add('fromName', $fromName);
        $mailHTMLObj->add('mail_content', $mailContent);
        $body = $mailHTMLObj->getContent();


        self::send_api();

        if (!$localTest){
            // Отчистка от тегов
            $altContent = str_replace(array("\n","\r","\t"), ' ', $mailContent);
            $altContent = preg_replace('~\s+~im', " ", $altContent);
            $altContent = preg_replace('~<hr[^>]*?>~im', "\n--------------------\n", $altContent);
            $altContent = preg_replace('~<br([^>]*?)></(div|p|h)([^>]*?)>~im', "\n", $altContent);
            $altContent = preg_replace('~<(h|p|div|)[^>]*?>~im', "\n    ", $altContent);
            $altContent = preg_replace('~\n{2,}~im', "\n\n", $altContent);
            $altContent = strip_tags($altContent);
            $altBodyObj = new \LPS\Container\WebContentContainer('mails/text.tpl');
            $altBodyObj->add('alt_mail_content', $altContent);
            $altBody = $altBodyObj->getContent();
			//filter emails
			$to_emails = array();
			if (is_array($to)){
				foreach ($to as $k => $t){
					if (is_numeric($k)){
						$email = $t;
						$name = '';
					}elseif(strpos($k, '@')){
						$email = $k;
						$name = $t;
					}else{
						$email = $t;
						$name = '';
					}
					$to_emails[preg_replace('~[^0-9\-_a-zA-Z\.@!#\$%&\'\*\+\/=\?\^\`\{\|\}\~]~', '', $email)] = $name;
				}
			}else{
				$to_emails = array($to => '');
			}
            $hidden_copy_to = NULL;
            if (!$notSendToDevelopers){
                $hidden_copy_to = \LPS\Config::getParametr('email', 'developers_email');
                if ($to == $hidden_copy_to){
                    $hidden_copy_to = NULL;
                }
            }
            // Create the message
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom(array($from => $fromName))
                ->setTo($to_emails)
                ->setBcc($hidden_copy_to)
                ->setBody($body, 'text/html')
                ->addPart($altBody, 'text/plain')
            ;
            // Optionally add any attachments
            if (!empty($attaches)){
                foreach ($attaches as $attach){
                    if (!empty($attach)){
                        $swift_attach = self::getAttachment($attach['file_name'], null, $attach['url']);
                        if (!($swift_attach instanceof \Swift_Attachment)){
                            throw new \LogicException('File is not instance of Swift_Attachment');
                        }
                        $message->attach($swift_attach);
                    }
                }
            }
            if (!$mailer->send($message, $failures)){
                return NULL;
            }
        }else{
            return $body;
        }
	}

    private static function getAttachment($file_name, $content_type, $url = null, $data = null){
        $attachment = null;
        if (!empty($url)){
            $attachment = \Swift_Attachment::fromPath($url, $content_type)->setFilename($file_name);
        }elseif(!empty($data)){
            $attachment = \Swift_Attachment::newInstance($data, $file_name, $content_type);
        }
        return $attachment;
    }
    /**
     * Логируем отправленные письма
     * @param array $main_params @see self::$main_fields
     * @param array $params все оставшиеся данные сериализуем
     * @return int|string - id или номер обращения
     */
    public static function log($main_params, $params){
        $data = array_intersect_key($main_params, array_flip(self::$main_fields));
        if (!empty($data[self::FIELD_PHONE])){
            $data[self::FIELD_PHONE] = preg_replace('~[^0-9]~', '', $data[self::FIELD_PHONE]);
        }
        if (isset($data['check_string'])){
            unset($data['check_string']);
        }
        if (isset($data['hash_string'])){
            unset($data['hash_string']);
        }
        $data['data'] = serialize($params);
        $db = \App\Builder::getInstance()->getDB();
        $id = $db->query('INSERT INTO `'.self::LOG_TABLE.'` SET ?a, `time`=NOW()', $data);
        return $id;
    }

    public static function searchLog($params, $start = null, $limit = null, &$count = 0){
        $db = \App\Builder::getInstance()->getDB();
        $order = '`time` DESC';
        if (!empty($params['order']) && is_array($params['order'])){
            foreach($params['order'] as $field => $desc){
                if (in_array($field, array('time'))){
                    $order = '`'.$field.'` ' . ($desc ? 'DESC' : 'ASC');
                }
                break;
            }
        }
        $result = $db->query('
            SELECT SQL_CALC_FOUND_ROWS `id`, `type`, `data`, `time`, `name`, `email`, `phone`, UNIX_TIMESTAMP(`time`) AS `timestamp`
            FROM `'.self::LOG_TABLE.'`
            WHERE 1
			{ AND `id` = ?d}
            { AND `email` LIKE ?s}
            { AND `name` LIKE ?s}
            { AND `phone` LIKE ?s}
            { AND `type` IN (?l)}
            { AND `type` != ?s}
            { AND `time` >= ?s}
            { AND `time` <= ?s}
            { AND `time` >= ?s}
            { AND `time` <= ?s}
            ORDER BY ' . $order . '
            { LIMIT ?d, ?d}',
				!empty($params['id']) ? $params['id'] : $db->skipIt(),
                !empty($params['email']) ? '%' . $params['email'] . '%' : $db->skipIt(),
                !empty($params['name']) ? '%' . $params['name'] . '%' : $db->skipIt(),
                !empty($params['phone']) ? '%' . $params['phone'] . '%' : $db->skipIt(),
                !empty($params['type']) ? (is_array($params['type']) ? $params['type'] : array($params['type'])) : $db->skipIt(),
                !empty($params['not_type']) ? $params['not_type'] : $db->skipIt(),
                !empty($params['time_start']) ? date('Y-m-d H:i:s', strtotime($params['time_start'])) : $db->skipIt(),
                !empty($params['time_end']) ? date('Y-m-d H:i:s', strtotime($params['time_end'])) : $db->skipIt(),
                !empty($params['date_start']) ? date('Y-m-d 00:00:00', strtotime($params['date_start'])) : $db->skipIt(),
                !empty($params['date_end']) ? date('Y-m-d 23:59:59', strtotime($params['date_end'])) : $db->skipIt(),
                !empty($start) ? $start : 0,
                !empty($limit) ? $limit : $db->skipIt()
            )->select();
        $count = $db->query('SELECT FOUND_ROWS()')->getCell();
		foreach ($result as &$res){
			$res['data'] = unserialize($res['data']);
		}
        return $result;
    }

    private static function send_api()
	{
        $departnment = array(
        	'773' => 19,
			'604' => 2
		);

		$feedback = false;
        $apartments = '';
		
		//$area_min   = '';
		//$area_max   = '';
		if(!empty($_POST['apartments'])) {
			$apartments = 773;
		} elseif(!empty($_POST['apartments_resale'])) {
			$apartments = 604;
		} elseif(!empty($_POST['feedbackType'])) {
			if($_POST['feedbackType'] == 'feedback') {
	        	$apartments = 773;
			} elseif($_POST['feedbackType'] == 'apart_request') {
	        	$apartments = 773;
			}
			elseif($_POST['feedbackType'] == 'view_apartments') {
	        	$apartments = 773;
			}
			elseif($_POST['feedbackType'] == 'owner') {
	        	$apartments = 773;
				
			}			
			elseif($_POST['feedbackType'] == 'flat_selection') {
	        	$apartments = 773;				
			}
		
		}
		
		
		
		
		
		

        if(!empty($departnment[$apartments])) {

	    	$name = '';
			if(!empty($_POST['author'])) {
				$name = $_POST['author'];
			}

			$phone = '';
			if(!empty($_POST['phone'])) {
				$phone = $_POST['phone'];
			}

			$email = '';
			if(!empty($_POST['email'])) {
				$email = $_POST['email'];
			}

			$message = '';
			if(!empty($_POST['message'])) {
				$message = $_POST['message'];
			}
			
			$address   = '';
			if(!empty($_POST['address'])) {
			$message .= '<br><b>Адрес:</b> '.$_POST['address'];
			}

			
			$bed_number  = '';
			if(!empty($_POST['bed_number'])) {
							$message .= '<br><b>Число спален:</b> '.$_POST['bed_number'];

			}

			$area  = '';
			if(!empty($_POST['area'])) {
							$message .= '<br><b>Площадь, м2:</b> '.$_POST['area'];

			}
			$area_min  = '';
			if(!empty($_POST['area_min'])) {
							$message .= '<br><b>Площадь, м2:</b> от '.$_POST['area_min'];
							$message .= ' до ' .$_POST['area_max'];
			}
			
			$price_min  = '';
			if(!empty($_POST['price_min'])) {
							$message .= '<br><b>Цена, млн руб.</b> от '.$_POST['price_min'];
							$message .= ' до ' .$_POST['price_max'];
			}
			

			$price  = '';
			if(!empty($_POST['price'])) {
							$message .= '<br><b>Цена, млн.руб:</b> '.$_POST['price'];

			}

			$type_kv = '';
			if(!empty($_POST['estate_type'])) {
				
			if ($_POST['estate_type'] = 119){
			$message .= '<br><b>Квартира:</b> В строящемся доме';
			}
				elseif ($_POST['estate_type'] = 120) {
				$message .= '<br><b>Квартира:</b> На вторичном рынке';
				}
			
			}
			
			$species = '';			
			if(!empty($_POST['species'])) {
							$message .= '<br><b>Видовая квартира:</b> Да';

			}


		
		
			
			
			
			
			
			
			
			
            $depart = $departnment[$apartments];

	    	$postdata = http_build_query(
			    array(
					'name' =>  $name, // Имя клиента
					'phone' => $phone, // Номер телефона
				    'email' => $email, // E-mail
				    'oauth_token' => '05fa7144fd39051c2b3e0e512f357239',
				    'source' => '80', // Элитный сайт
				    'department' => $depart,  // Элитная недвижимость: department=19,
											  // Вторичное жильё: department=2
				    'message' => $message // текст сообщения с заявки
				    //'referrer' => '', // Адрес страницы, с которой перешли на сайт
			    )
			);

			$opts = array('http' =>
			    array(
			        'method'  => 'POST',
			        'header'  => 'Content-type: application/x-www-form-urlencoded',
			        'content' => $postdata
			    )
			);

			$context  = stream_context_create($opts);

			$result = file_get_contents('http://m16.kv1.ru/api/orders/post', false, $context);
		}
	}

}