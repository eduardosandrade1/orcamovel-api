<?php

namespace App\Actions;

use App\Models\Api\Client;
use App\Traits\IdentifyContact;

final class CreateClientAction {

  use IdentifyContact;

  public function run($data) {
    $client = new Client();

    $client->name = $data->clientName;

    if ($this->isEmail($client->contactValue)) {
      $client->email = $client->contactValue;
    }

    if ($this->isPhone($client->contactValue)) {
      $client->phone = $client->contactValue;
    }

    $client->save();

    return $client->id;
  }

}