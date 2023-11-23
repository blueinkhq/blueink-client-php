<?php
# NOTE
# considering move all the endpoint to class with static function.
# implement with bundle endpoints first then will make for other
namespace Blueink\ClientSDK;
/**
 * List all of the bundle enpoints
 */
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
/**
 * List all of the person endpoints
 */
class PersonEndpoints
{
	public static function create()
	{
		return "/persons/";
	}
	public static function list()
	{
		return "/persons/";
	}
	public static function retrieve(string $person_id)
	{
		return "/persons/$person_id/";
	}
	public static function update(string $person_id)
	{
		return "/persons/$person_id/";
	}
	public static function delete(string $person_id)
	{
		return "/persons/$person_id/";
	}
}
/**
 * List all of the packet endpoints
 */
class PacketEndpoints
{
	public static function embed_url(string $packet_id)
	{
		return "/packets/$packet_id/embed_url";
	}
	public static function update(string $packet_id)
	{
		return "/packets/$packet_id/";
	}
	public static function remind(string $packet_id)
	{
		return "/packets/$packet_id/remind/";
	}
	public static function retrieve_coe(string $packet_id)
	{
		return "/packets/$packet_id/coe/";
	}
}


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
