<?php
/**
 * Created by:
 * User: svetlanakartysh
 * Date: 22.12.2020
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/")
 */
class IndexController extends AbstractController
{
	/**
	 *
	 * @Route("/", name="index")
	 */
	public function indexAction(Request $request)
	{
		$accessToken = $this->getClientCredentials();

		$urls = [
			'https://www.instagram.com/p/BzsYRbuJz9I/',
			'https://www.instagram.com/p/BPnHCKHgt0I/',
			'https://www.instagram.com/p/B5QTg4DpiCa/',
			'https://www.instagram.com/p/B3d8O1fpCXb/',
			'https://www.instagram.com/p/BvYyBI2BnPC/',
			'https://www.instagram.com/p/CJDFlDCgvbb/',
			'https://www.instagram.com/p/CGnGx7OAN18/',
		];

		$data = [];
		if ($accessToken) {
			foreach ($urls as $url)
				$data[] = $this->getEmbedData($accessToken, $url);
		}

		return $this->render('index.html.twig', ['data' => $data]);
	}


	/**
	 * Получает массив данных
	 *
	 * @param $token
	 *
	 * @return array
	 */
	private function getMyData($token)
	{
		/** @var HttpClient $client */
		$client = HttpClient::create();

		try {
			$response = $client->request('GET', "https://graph.instagram.com/me/media?fields=id,media_type,media_url,caption,timestamp,thumbnail_url,permalink&access_token="
				. $token
			);

		} catch (\Exception $e) {
			return $errorData = $e->getMessage();
		}

		$content = [];

		if ($response->getStatusCode() == 200) {
			$content = $response->toArray();
		}

		return $content;
	}


	/**
	 * Получает массив данных
	 *
	 * @param $token string
	 * @param $url string
	 *
	 * @return array
	 */
	private function getEmbedData($token, $url)
	{
		/** @var HttpClient $client */
		$client = HttpClient::create();

		try {
			$response = $client->request('GET', "https://graph.facebook.com/v9.0/instagram_oembed?url=".$url."&access_token="
				. $token
			);

		} catch (\Exception $e) {
			return $errorData = $e->getMessage();
		}

		$content = [];
		if ($response->getStatusCode() == 200) {
			$content = $response->toArray();
		}

		return $content;
	}


	public function getClientCredentials()
	{
		/** @var HttpClient $client */
		$client = HttpClient::create();

		try {
			$response = $client->request('GET', 'https://graph.facebook.com/oauth/access_token?client_id='
				. $this->get('parameter_bag')->get('facebook_stream_client_id')
				. '&client_secret='
				. $this->get('parameter_bag')->get('facebook_stream_client_secret')
				. '&grant_type=client_credentials'
			);


		} catch (\Exception $e) {
			return $errorData = $e->getMessage();
		}

		$content = [];
		if ($response->getStatusCode() == 200) {
			$content = $response->toArray();
		}

		return $content['access_token'];

	}


}