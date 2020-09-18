<?php

declare(strict_types=1);

namespace App\Tests\Controller;

trait BillServiceControllerTrait
{
    private function buildSoapRequest(array $parameters)
    {
        $template = file_get_contents(__DIR__.'/../Resources/send_bill_template.xml');
        $search = array_keys($parameters);
        $search = array_map(fn ($key) => "%$key%", $search);

        return str_replace($search, array_values($parameters), $template);
    }
}