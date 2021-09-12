<?php

// src/Service/DataService.php

namespace App\Service;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class DataService
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container=$container;
    }

    public function getPaginatedData($request)
    {
        $session = $request->getSession();
        if($session->get('trimmedData')){
            $trimmedData = $session->get('trimmedData');
        }
        else{
            $trimmedData = $this->getTrimmedData();
            $session->set('trimmedData', $trimmedData);
        }
        
        $pagenator = $this->container->get('knp_paginator');
        $results = $pagenator->paginate(
            $trimmedData,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10)
        );
        return $results;
    }

    public function getTrimmedData()
    {
        $csvData = $this->readCSV();
        $index = 1;
        foreach ($csvData as $key => $val)
        {
            $pizza = implode('&NewLine;', $val);
            $pizza = str_replace(array('"'), '',$pizza);
            $pieces = explode(";", $pizza);

            $row['id'] = $pieces[0];
            $row['manufacturer'] = $pieces[1];
            $row['name'] = $pieces[2];
            $row['additional'] = isset($pieces[3]) ? $pieces[3] : null;
            $row['price'] = isset($pieces[4]) ? $pieces[4] : null;
            $row['availability'] = isset($pieces[5]) ? $pieces[5] : null;
            $row['product_image'] = isset($pieces[6]) ? $pieces[6] : null;

            $data[$index] = $row;
            $index = $index + 1;
        }
        return $data;
    }    

    public function readCSV()
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $csvData = $serializer->decode(file_get_contents('db_tires.csv'), 'csv');
        
        return $csvData;
    }
}