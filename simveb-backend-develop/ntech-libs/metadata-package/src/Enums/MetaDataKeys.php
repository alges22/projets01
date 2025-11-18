<?php
namespace Ntech\MetadataPackage\Enums;

use App\Traits\EnumToArray;

enum MetaDataKeys
{
    use EnumToArray;

    case active_account_min_balance;
    case active_account_min_transaction;
    case number_of_month_active_account_transaction; //Number of month to consider when calculating transactions
    case number_of_customer_created_for_bonus;
    case account_inactivity_time_limit;
    case supplies_default_amount;
    case time_of_supplies;
    case auth_params;

    case space_token_duration;
    case duration_params;
    case password_expiration_control;
    case max_password_histories;
    case otp_duration;
}
