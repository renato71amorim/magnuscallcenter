<?php
class UpdateMysqlCommand extends ConsoleCommand
{
    public $config;
    public $success;

    public function run($args)
    {

        $version = $this->config['global']['version'];

        echo $version;

        if ($version == '3.0.0') {

            $sql = "ALTER TABLE  `pkg_campaign` ADD  `call_limit` INT( 11 ) NOT NULL DEFAULT  '0',
                            ADD  `call_next_try` INT( 11 ) NOT NULL DEFAULT  '30',
                            ADD  `predictive` INT( 11 ) NOT NULL DEFAULT  '0';
                    ALTER TABLE `pkg_breaks` CHANGE `start_time` `start_time` TIME NOT NULL DEFAULT '00:00:00';
                    ALTER TABLE `pkg_breaks` CHANGE `stop_time` `stop_time` TIME NOT NULL DEFAULT '00:00:00';
                    ALTER TABLE  `pkg_phonenumber` ADD  `cpf` VARCHAR( 15 ) NOT NULL DEFAULT  '' AFTER  `dni`;
            ";
            $this->executeDB($sql);

            $version = '3.0.1';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.0.1') {

            $sql = "ALTER TABLE  `pkg_campaign` ADD  `allow_neighborhood` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `allow_city`;
            ALTER TABLE  `pkg_phonenumber` ADD  `neighborhood` VARCHAR( 50 ) NOT NULL DEFAULT  '' AFTER  `city`;
            ALTER TABLE  `pkg_phonenumber` ADD  `try` INT( 1 ) NOT NULL DEFAULT  '0';
            ";
            $this->executeDB($sql);

            $version = '3.0.2';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }
        if ($version == '3.0.2') {

            $sql = "INSERT INTO pkg_configuration VALUES (NULL, 'Tolerancia para mais e para menos para pausas obrigatorias', 'break_tolerance', '3', 'Tolerancia para mais e para menos para pausas obrigatorias', 'global', '1');;
            ";
            $this->executeDB($sql);

            $version = '3.0.3';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.0.3') {

            $sql = "ALTER TABLE `pkg_logins_campaign` ADD CONSTRAINT `fk_pkg_logins_campaig_pkg_breaks` FOREIGN KEY (`id_breaks`) REFERENCES `pkg_breaks` (`id`);
            ALTER TABLE  `pkg_breaks` ADD  `status` TINYINT( 1 ) NOT NULL DEFAULT  '1'
            ";
            $this->executeDB($sql);

            $version = '3.0.4';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.0.4') {

            $sql = "UPDATE `pkg_configuration` SET `config_description` = '1 to active, 0 to inactive ' WHERE config_key = 'amd';

            UPDATE `pkg_configuration` SET `status` = '0';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'base_language';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'version';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'admin_email';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'portabilidadeUsername';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'portabilidadePassword';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'operator_next_try';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'updateAll';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'campaign_limit';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'tardanza';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'valor_colectivo';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'valor_hora_zero';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'valor_hora';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'valor_falta';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'notify_url_after_save_number';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'notify_url_category';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'record_call';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'dialcommand_param';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'MixMonitor_format';
            ALTER TABLE `pkg_category` ADD `color` VARCHAR(7) NOT NULL DEFAULT '#ffffff' AFTER `use_in_efetiva`;



            UPDATE `pkg_category` SET `color` = '#FF0000' WHERE id = 0;
            UPDATE `pkg_category` SET `color` = '#339966' WHERE id = 1;
            UPDATE `pkg_category` SET `color` = '#ddb96d' WHERE id = 2;
            UPDATE `pkg_category` SET `color` = '#FF99CC' WHERE id = 3;
            UPDATE `pkg_category` SET `color` = '#ab6b40' WHERE id = 4;
            UPDATE `pkg_category` SET `color` = '#800080' WHERE id = 5;
            UPDATE `pkg_category` SET `color` = '#00FF00' WHERE id = 6;
            UPDATE `pkg_category` SET `color` = '#d9d1a8' WHERE id = 7;
            UPDATE `pkg_category` SET `color` = '#8d5ed5' WHERE id = 8;
            UPDATE `pkg_category` SET `color` = '#993366' WHERE id = 9;
            UPDATE `pkg_category` SET `color` = '#FF0000' WHERE id = 10;
            UPDATE `pkg_category` SET `color` = '#99CCFF' WHERE id = 11;


            ";
            $this->executeDB($sql);

            $sql = "DELETE FROM pkg_category WHERE name = 'Inactivo' AND status = 0;";
            $this->executeDB($sql);

            $sql = "INSERT INTO `pkg_category` VALUES (99,'Inativo','',0,0);
            UPDATE `pkg_category` SET `id` = '0' WHERE `id` = 99;";
            $this->executeDB($sql);

            $sql = "ALTER TABLE  `pkg_category` ADD  `type` TINYINT( 1 ) NOT NULL DEFAULT  '1';";
            $this->executeDB($sql);

            $sql = "UPDATE  `pkg_category` SET  `type` =  '0' WHERE  `pkg_category`.`id` =0;";
            $this->executeDB($sql);

            $version = '3.0.5';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.0.5') {

            $sql = "ALTER TABLE `pkg_campaign` ADD `open_url` VARCHAR(200) NOT NULL DEFAULT '' AFTER `status`;
            ";
            $this->executeDB($sql);

            $version = '3.0.6';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.0.6') {

            $sql = "
                ALTER TABLE `pkg_phonenumber` CHANGE `endereco_complementar` `address_complement` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
                ALTER TABLE `pkg_massive_call_phonenumber` CHANGE `endereco_complementar` `address_complement` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
                ALTER TABLE `pkg_campaign` CHANGE `allow_endereco_complementar` `allow_address_complement` INT(11) NOT NULL DEFAULT '0';

                ALTER TABLE `pkg_phonenumber`
                ADD `address_number` INT(10) NULL DEFAULT NULL AFTER `address_complement`,
                ADD `beneficio_especie` VARCHAR(60) NULL DEFAULT NULL,
                ADD `valor_proposta` INT(10) NULL DEFAULT NULL,
                ADD `valor_parcela` INT(10) NULL DEFAULT NULL;

                ALTER TABLE  `pkg_campaign`
                ADD `allow_cpf` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `allow_dni`,
                ADD `allow_address_number` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `allow_address`,
                ADD `allow_beneficio_especie` INT( 11 ) NOT NULL DEFAULT  '0',
                ADD `allow_valor_proposta` INT( 11 ) NOT NULL DEFAULT  '0',
                ADD `allow_valor_parcela` INT( 11 ) NOT NULL DEFAULT  '0';


            ";
            $this->executeDB($sql);

            $version = '3.0.7';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.0.7') {

            $sql = "
            ALTER TABLE `pkg_campaign` ADD `allow_option_6` VARCHAR(100) NULL DEFAULT NULL AFTER `allow_option_5`;
            ALTER TABLE `pkg_campaign` ADD `allow_option_6_type` VARCHAR(200) NULL DEFAULT NULL AFTER `allow_option_5_type`;
            ALTER TABLE `pkg_phonenumber` ADD `option_6` VARCHAR(80) NULL DEFAULT NULL AFTER `option_5`;

            ALTER TABLE `pkg_campaign` ADD `allow_option_7` VARCHAR(100) NULL DEFAULT NULL AFTER `allow_option_6`;
            ALTER TABLE `pkg_campaign` ADD `allow_option_7_type` VARCHAR(200) NULL DEFAULT NULL AFTER `allow_option_6_type`;
            ALTER TABLE `pkg_phonenumber` ADD `option_7` VARCHAR(80) NULL DEFAULT NULL AFTER `option_6`;

            ALTER TABLE `pkg_campaign` ADD `allow_option_8` VARCHAR(100) NULL DEFAULT NULL AFTER `allow_option_7`;
            ALTER TABLE `pkg_campaign` ADD `allow_option_8_type` VARCHAR(200) NULL DEFAULT NULL AFTER `allow_option_7_type`;
            ALTER TABLE `pkg_phonenumber` ADD `option_8` VARCHAR(80) NULL DEFAULT NULL AFTER `option_7`;


            #add new field
            ALTER TABLE `pkg_phonenumber` ADD `conta_tipo` VARCHAR(20) NULL DEFAULT NULL AFTER `banco`;
            ALTER TABLE `pkg_campaign` ADD `allow_conta_tipo` INT(11) DEFAULT '0' AFTER `allow_banco`;

            ALTER TABLE `pkg_phonenumber` ADD `credit_card_name` VARCHAR(50) NULL DEFAULT NULL;
            ALTER TABLE `pkg_campaign` ADD `allow_credit_card_name` INT(11) DEFAULT '0';

            ALTER TABLE `pkg_phonenumber` ADD `credit_card_type` VARCHAR(30) NULL DEFAULT NULL;
            ALTER TABLE `pkg_campaign` ADD `allow_credit_card_type` INT(11) DEFAULT '0' ;

            ALTER TABLE `pkg_phonenumber` ADD `credit_card_number` VARCHAR(50) NULL DEFAULT NULL ;
            ALTER TABLE `pkg_campaign` ADD `allow_credit_card_number` INT(11) DEFAULT '0' ;

            ALTER TABLE `pkg_phonenumber` ADD `credit_card_code` VARCHAR(10) NULL DEFAULT NULL;
            ALTER TABLE `pkg_campaign` ADD `allow_credit_card_code` INT(11) DEFAULT '0' ;
            ";
            $this->executeDB($sql);

            $version = '3.0.8';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.0.8') {

            $sql = "ALTER TABLE `pkg_phonenumber` CHANGE `address_number` `address_number` VARCHAR(10) NULL DEFAULT NULL;
                    ALTER TABLE `pkg_phonenumber` CHANGE `name` `name` CHAR(100) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;
            ";
            $this->executeDB($sql);

            $version = '3.0.9';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.0.9') {

            $sql = "INSERT INTO pkg_configuration VALUES (NULL, 'Abrir URL quando operador receber a chamada', 'notify_url_when_receive_number', '', 'Abrir URL quando operador receber a chamada', 'global', '1');
            ";
            $this->executeDB($sql);

            $version = '3.1.0';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.1.0') {

            $sql = "ALTER TABLE `pkg_user` ADD `allow_direct_call_campaign` INT(11) NULL DEFAULT NULL AFTER `auto_load_phonenumber`;";
            $this->executeDB($sql);

            $version = '3.1.1';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.1.1') {

            $sql = "ALTER TABLE `pkg_campaign` ADD `open_url_when_answer_call` VARCHAR(200) NULL DEFAULT NULL AFTER `open_url`;";
            $this->executeDB($sql);

            $sql = "DELETE FROM `pkg_configuration` WHERE config_key = 'notify_url_when_receive_number'";
            $this->executeDB($sql);

            $version = '3.1.2';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.1.2') {

            $sql = "ALTER TABLE `pkg_massive_call_phonenumber` ADD `res_dtmf` INT(11) NULL DEFAULT NULL AFTER `timeCall`;";
            $this->executeDB($sql);

            $sql = "ALTER TABLE `pkg_massive_call_phonenumber` ADD `queue_status` VARCHAR(50) NULL DEFAULT NULL AFTER `res_dtmf`;";
            $this->executeDB($sql);

            $sql = "ALTER TABLE `pkg_massive_call_phonenumber` ADD `id_user` INT(11) NULL DEFAULT NULL AFTER `id`;";
            $this->executeDB($sql);

            $sql = "INSERT INTO pkg_module VALUES (NULL, 't(''Massive Call Report'')', 'massivecallreport', 'prefixs', 5)";
            $this->executeDB($sql);
            $idServiceModule = Yii::app()->db->lastInsertID;

            $sql = "INSERT INTO pkg_group_module VALUES ((SELECT id FROM pkg_group_user WHERE id_user_type = 1 LIMIT 1), '" . $idServiceModule . "', 'r', '1', '1', '1');";
            $this->executeDB($sql);

            $version = '3.1.3';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.1.3') {

            $sql = "ALTER TABLE `pkg_predictive_gen` ADD `amd` TINYINT(1) NOT NULL DEFAULT '0' AFTER `ringing_time`;";
            $this->executeDB($sql);

            $sql = "INSERT INTO `callcenter`.`pkg_category` (`id`, `name`, `description`, `status`, `use_in_efetiva`, `color`, `type`) VALUES ('-2', 'AMD', NULL, '1', '0', '#ffffff', '1');";
            $this->executeDB($sql);

            $version = '3.1.4';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);

            exec('echo "" >> /etc/asterisk/extensions.ael');
            exec('echo "context magnuscallcenterpredictive {" >> /etc/asterisk/extensions.ael');
            exec('echo "    _X. => {" >> /etc/asterisk/extensions.ael');
            exec('echo "        if (\"\${AMD}\"==\"1\")" >> /etc/asterisk/extensions.ael');
            exec('echo "        {" >> /etc/asterisk/extensions.ael');
            exec('echo "            Answer();" >> /etc/asterisk/extensions.ael');
            exec('echo "            Background(silence/1);" >> /etc/asterisk/extensions.ael');
            exec('echo "            AMD();" >> /etc/asterisk/extensions.ael');
            exec('echo "            Verbose(\${AMDSTATUS});" >> /etc/asterisk/extensions.ael');
            exec('echo "        }" >> /etc/asterisk/extensions.ael');
            exec('echo "        AGI(/var/www/html/callcenter/agi.php);" >> /etc/asterisk/extensions.ael');
            exec('echo "        Hangup();" >> /etc/asterisk/extensions.ael');
            exec('echo "    }" >> /etc/asterisk/extensions.ael');
            exec('echo "}" >> /etc/asterisk/extensions.ael');
            exec('echo "" >> /etc/asterisk/extensions.ael');
        }

        if ($version == '3.1.4') {

            $sql = "ALTER TABLE `pkg_massive_call_phonenumber` ADD `dial_date` DATETIME NULL DEFAULT NULL AFTER `creationdate`;";
            $this->executeDB($sql);

            $version = '3.1.5';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.1.5') {

            $sql = "ALTER TABLE `pkg_predictive` ADD `id_campaign` INT(11) NULL DEFAULT NULL AFTER `id`;
                    ALTER TABLE `pkg_predictive` ADD `amd` INT(11) NOT NULL DEFAULT '0';";
            $this->executeDB($sql);

            $version = '3.1.6';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.1.6') {

            $sql = "ALTER TABLE `pkg_user` ADD `force_logout` INT(1) NOT NULL DEFAULT '0' AFTER `id_campaign`;";
            $this->executeDB($sql);

            $version = '3.1.7';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }
        if ($version == '3.1.7') {

            $sql = "UPDATE `pkg_configuration` SET `status` = 1 WHERE config_key = 'amd'";
            $this->executeDB($sql);

            $version = '3.1.8';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.1.8') {

            $sql = "INSERT INTO `callcenter`.`pkg_category` (`id`, `name`, `description`, `status`, `use_in_efetiva`, `color`, `type`) VALUES ('-3', 'LEAVE QUEUE', NULL, '0', '0', '#ffffff', '1');";
            $this->executeDB($sql);

            $version = '3.2.0';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

    }

    private function executeDB($sql)
    {
        try {
            Yii::app()->db->createCommand($sql)->execute();
        } catch (Exception $e) {

        }
    }
}
