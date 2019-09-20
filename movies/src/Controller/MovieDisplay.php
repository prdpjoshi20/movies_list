<?php

namespace Drupal\movies\Controller;

class MovieDisplay {
  public function api_call($args) {
    $url = 'http://www.omdbapi.com/?&apikey=7aedd340' . $args;
    $method = 'GET';
    $curl = curl_init();

    switch ($method) {
      case "POST":
        curl_setopt($curl, CURLOPT_POST, 1);
        break;
      case "PUT":
        curl_setopt($curl, CURLOPT_PUT, 1);
        break;
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);

    curl_close($curl);
    return $result;
  }

  public function movie_language($args) {
    $url = '&i=' . $args;
    $result = $this->api_call($url);
    $response = json_decode($result, true);
    return $response;
  }

  public function movie_display() {
    for ($i = 1; $i <= 3; $i++) {
      if ($i >= 3) {
        $url = '&s=Article&type=movie';
      }
      else {
        $url = '&s=movie&y=2019&type=movie&page=' . $i;
      }
      $result = $this->api_call($url);
      $response = json_decode($result, true);
      $item = array();
      $count = 0;
      foreach ($response['Search'] as $key => $val) {
//        $i
        $language = NULL;
        $genric = null;
        $lan_resp = $this->movie_language($val['imdbID']);
        $language = $lan_resp['Language'];
        $genric = $lan_resp['Genre'];

        if ($count <= 6) {
          $items['Latest'][] = array(
           'name' => $val['Title'],
           'poster' => $val['Poster'],
           'language' => $language,
          );
        }
        if (strpos($language, ',') !== false) {
          $lan_array = explode(',', $language);
        }
        else {
          $lan_array = array($language);
        }

        if ($val['Poster'] != 'N/A') {
          foreach ($lan_array as $lan) {
            $lan = trim($lan);
            $items[$lan][] = array(
             'name' => $val['Title'],
             'poster' => $val['Poster'],
             'language' => $lan,
            );
          }
        }
        if (strpos($genric, ',') !== false) {
          $genric_array = explode(',', $genric);
        }
        else {
          $genric_array = array($genric);
        }
        foreach ($genric_array as $lan) {
          $lan = trim($lan);
          $items[$lan][] = array(
           'name' => $val['Title'],
           'poster' => $val['Poster'],
           'language' => $language,
          );
        }
        $count++;
      }
    }
    return array(
     '#theme' => 'movies_list',
     '#items' => $items,
     '#title' => '',
    );
  }

}
