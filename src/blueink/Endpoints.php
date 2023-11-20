<?php
# NOTE
# considering move all the endpoint to class with static function.
# implement with bundle endpoints first then will make for other
namespace Blueink\ClientSDK;

class BundleEndpoints
{
	public static function create()
	{
		return "/bundles/";
	}
	public static function list()
	{
		return "/bundles/";
	}
	public static function retrieve(string $bundle_id)
	{
		return "/bundles/$bundle_id/";
	}
	public static function cancel(string $bundle_id)
	{
		return "/bundles/$bundle_id/cancel/";
	}
	public static function list_events(string $bundle_id)
	{
		return "/bundles/$bundle_id/events/";
	}
	public static function list_files(string $bundle_id)
	{
		return "/bundles/$bundle_id/files/";
	}
	public static function list_data(string $bundle_id)
	{
		return "/bundles/$bundle_id/data/";
	}
}

const PERSON = array(
	"CREATE" => "/persons/",
	"LIST" => "/persons/",
	"RETRIEVE" => "/persons/{person_id}",
	"UPDATE" => "/persons/{person_id}",
	"CANCEL" => "/persons/{person_id}",
);

const PACKETS = array(
	"EMBED_URL" => "/packets/{packet_id}/embed_url",
	"UPDATE" => "/packets/{packet_id}/",
	"REMIND" => "/packets/{packet_id}/remind/",
	"RETRIEVE_COE" => "/packets/{packet_id}/coe/",
);

const TEMPLATES = array(
	"LIST" => "/templates",
	"RETRIEVE" => "/templates/{template_id}/",
);

const WEBHOOKS = array(
	"CREATE" => "/webhooks/",
	"LIST" => "/webhooks/",
	"RETRIEVE" => "/webhooks/{webhook_id}/",
	"UPDATE" => "/webhooks/{webhook_id}/",
	"DELETE" => "/webhooks/{webhook_id}/",

	"CREATE_HEADER" => "/webhooks/headers/",
	"LIST_HEADER" => "/webhooks/headers/",
	"RETRIEVE_HEADER" => "/webhooks/headers/{webhook_header_id}/",
	"UPDATE_HEADER" => "/webhooks/headers/{webhook_header_id}/",
	"DELETE_HEADER" => "/webhooks/headers/{webhook_header_id}/",

	"LIST_EVENTS" => "/webhooks/events/",
	"RETRIEVE_EVENT" => "/webhooks/events/{webhook_event_id}/",

	"LIST_DELIVERIES" => "/webhooks/deliveries/",
	"RETRIEVE_DELIVERY" => "/webhooks/deliveries/{webhook_delivery_id}/",

	"RETRIEVE_SECRET" => "/webhooks/secret/",
	"REGENERATE_SECRET" => "/webhooks/secret/regenerate/",
);
