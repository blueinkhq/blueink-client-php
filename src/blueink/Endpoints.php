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
		return "/packets/$packet_id/embed_url/";
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
/**
 * List all of the template endpoints
 */
class TemplateEndpoints {
	public static function list() {
		return "/templates/";
	}
	public static function retrieve(string $template_id) {
		return "/templates/$template_id/";
	}
}
/**
 * List all of the webhook endpoints
 */
class WebhookEndpoints {
	public static function create() {
		return "/webhooks/";
	}
	public static function list() {
		return "/webhooks/";
	}
	public static function retrieve(string $webhook_id) {
		return "/webhooks/$webhook_id/";
	}
	public static function update(string $webhook_id) {
		return "/webhooks/$webhook_id/";
	}
	public static function delete(string $webhook_id) {
		return "/webhooks/$webhook_id/";
	}
	public static function create_header() {
		return "/webhooks/headers/";
	}
	public static function list_headers() {
		return "/webhooks/headers/";
	}
	public static function retrieve_header(string $webhook_header_id) {
		return "/webhooks/headers/$webhook_header_id/";
	}
	public static function update_header(string $webhook_header_id) {
		return "/webhooks/headers/$webhook_header_id/";
	}
	public static function delete_header(string $webhook_header_id) {
		return "/webhooks/headers/$webhook_header_id/";
	}
	public static function list_events() {
		return "/webhooks/events/";
	}
	public static function retrieve_event(string $webhook_event_id) {
		return "/webhooks/events/$webhook_event_id/";
	}
	public static function list_deliveries() {
		return "/webhooks/deliveries/";
	}
	public static function retrieve_delivery(string $webhook_delivery_id) {
		return "/webhooks/deliveries/$webhook_delivery_id/";
	}
	public static function retrieve_secret() {
		return "/webhooks/secret/";
	}
	public static function regenerate_secret() {
		return "/webhooks/secret/regenerate/";
	}
}
