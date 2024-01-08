<?php

namespace Codemanas\WooPreviewEmails;

class UnsupportedEmails {

	public static function unset_booking_emails() {
		// logic to unset booking emails
		//Filtering out booking emails becuase it won't work from this plugin
		//Buy PRO version if you need this capability
		return array(
			'WC_Email_New_Booking',
			'WC_Email_Booking_Reminder',
			'WC_Email_Booking_Confirmed',
			'WC_Email_Booking_Notification',
			'WC_Email_Booking_Cancelled',
			'WC_Email_Admin_Booking_Cancelled',
			'WC_Email_Booking_Pending_Confirmation',
		);
	}

	public static function unset_subscription_emails() {
		//Filtering out subscription emails becuase it won't work from this plugin
		//Buy PRO version if you need this capability
		return array(
			'WCS_Email_New_Renewal_Order',
			'WCS_Email_New_Switch_Order',
			'WCS_Email_Processing_Renewal_Order',
			'WCS_Email_Completed_Renewal_Order',
			'WCS_Email_Completed_Switch_Order',
			'WCS_Email_Customer_Renewal_Invoice',
			'WCS_Email_Cancelled_Subscription',
			'WCS_Email_Expired_Subscription',
			'WCS_Email_On_Hold_Subscription',
		);
	}

	public static function unset_membership_emails(  ) {
		//Filtering out membership emails because it won't work from this plugin
		//Buy PRO version if you need this capability
		return array(
			'WC_Memberships_User_Membership_Note_Email',
			'WC_Memberships_User_Membership_Ending_Soon_Email',
			'WC_Memberships_User_Membership_Ended_Email',
			'WC_Memberships_User_Membership_Renewal_Reminder_Email',
			'WC_Memberships_User_Membership_Activated_Email',
		);
	}
}