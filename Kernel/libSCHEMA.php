<?php
	class DBSchema{

		private static $data_types = array(
				1 => "INT",
				2 => "STR",
				3 => "DATE",
				4 => "TEXT",
				5 => "SHA1",
		);
		
		private static $crm_prospects = array(
				"id_prospect" => 1,
				"first_name" => 2,
				"last_name" => 2,
				"phone_number" => 2,
				"mobile_number" => 2,
				"email" => 2,
				"referrer_id" => 1,
				"reason_id" => 1,
				"assigned_to" => 1,
				"agreement_id" => 1,
				"initial_level" => 1,
		);
		
		private static $crm_followup = array(
				"id_followup" => 1,
				"id_prospect" => 1,
				"record_date" => 3,
				"sale_stg_id" => 1,
				"next_stg_id" => 1,
				"followup_date" => 3,
				"user_id" => 1,
				"is_current" => 1,
				"comments" => 4,
				"is_closed" => 1,
				"extra_data" => 2,
		);
		
		private static $crm_users = array(
				"user_id" => 1,
				"user_name" => 2,
				"user_key" => 5,
				"last_login" => 3,
				"enabled" => 1,
				"user_type" => 1,
		);
		
		private static $crm_referrers = array(
				"referrer_id" => 1,
				"description" => 2,
				"visible" => 1,
		);
		
		private static $crm_reasons = array(
				"id_reason" => 1,
				"description" => 2,
				"visible" => 1,
		);
		
		private static $crm_sale_stages = array(
				"id_stage" => 1,
				"description" => 2,
				"success_ratio" => 1,
				"visible" => 1,
		);
		
		private static $crm_teachers = array(
				"id_teacher" => 1,
				"first_name" => 2,
				"last_name" => 2,
				"visible" => 1,
		);
		
		private static $crm_classroom = array(
				"id_classroom" => 1,
				"description" => 2,
				"visible" => 1,
		);
		
		private static $crm_class_level = array(
				"id_level" => 1,
				"description" => 2,
				"visible" => 1,
		);
		
		private static $crm_class_schedule = array(
				"id_schedule" => 1,
				"id_day" => 1,
				"start_time" => 3,
				"end_time" => 3,
				"id_teacher" => 1,
				"id_classroom" => 1,
				"id_level" => 1,
				"available" => 1,
		);
		
		private static $crm_agreements = array(
				"id_agreement" => 1,
				"company_name" => 2,
				"agreement" => 2,
				"visible" => 1,
		);
		
		private static $crm_audit = array(
				"id_audit" => 1,
				"id_prospect" => 1,
				"id_user" => 1,
				"audit_date" => 3,
				"audit_data" => 4,
		);
		
		private static $crm_demo_class = array(
				"id_record" => 1,
				"id_prospect" => 1,
				"class_date" => 3,
				"id_classroom" => 1,
				"id_level" => 1,
				"id_teacher" => 1,
				"show_up" => 1,
		);
		
		public static function getDataType($table, $field){
			$table = preg_replace('/-/',"_",$table);
			return (array_key_exists($field,self::${$table})) ? self::${$table}[$field] : 0;
		}
		
		public static function getKeyField($table){
			$table = preg_replace('/-/',"_",$table);
			$wrkrnd = array_keys(self::${$table});
			return array_shift($wrkrnd);
		}
		
		public static function getTableNameByID($id){
			$table = "NT";
			switch($id*1){
				case 1:
					$table = "crm_referrers";
					break;
				case 2:
					$table = "crm_sale-stages";
					break;
				case 3:
					$table = "crm_reasons";
					break;
				case 4:
					$table = "crm_teachers";
					break;
				case 5:
					$table = "crm_classroom";
					break;
				case 6:
					$table = "crm_class-level";
					break;
				case 7:
					$table = "crm_agreements";
					break;
				case 8:
					$table = "crm_users";
					break;
			}
			return $table;
		}

	}
?>