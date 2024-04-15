<?php
namespace Attend;


use Attend\Database\ClassroomQuery;
use Attend\PropelEngine\PropelEngine;
use Slim\Container;
use Slim\App;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\TwigFunction;


// Find the DateTime object representing the Monday closest to the input date
function getMonday(\DateTime $d)
{
    $dw = $d->format('N');
    switch ($dw) {
        case 1:
            break;
        case 2:  // Tuesday
            $d = $d->sub(new \DateInterval('P1D'));
            break;
        case 3:  // Wednesday
            $d = $d->sub(new \DateInterval('P2D'));
            break;
        case 4:  // Thursday
            $d = $d->sub(new \DateInterval('P3D'));
            break;

        case 5:  // Friday
            $d = $d->add(new \DateInterval('P3D'));
            break;
        case 6:  // Saturday
            $d = $d->add(new \DateInterval('P2D'));
            break;
        case 7:  // Sunday
            $d = $d->add(new \DateInterval('P1D'));
            break;

        default:
            throw new \Exception(sprintf('Unknown day of the week "%d"', $dw));
            break;
    }

    return $d;
}


/********************************************************************************
 * Main Script
 ********************************************************************************/

require '../lib/bootstrap.php';
$config                          = bootstrap();
$config[ 'displayErrorDetails' ] = true;
$dependencies                    = new Container([
    'settings' => $config
]);

$app = new App($dependencies);

$engine = new PropelEngine();
$engine->connect($config[ 'db' ]);

////////////////////////////////////////////////////////////////////////////////////////////////////
// Routing for Web App Pages
////////////////////////////////////////////////////////////////////////////////////////////////////
$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $loader = new \Twig_Loader_Filesystem('../templates');
    $twig   = new \Twig_Environment($loader, array(
        'cache' => false
    ));

    $response->getBody()->write($twig->render('index.html.twig', []));

    return $response;
});


$app->get('/attendance', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $loader = new \Twig_Loader_Filesystem('../templates');
    $twig   = new \Twig_Environment($loader, array(
        'cache' => false
    ));

    // Get the text version of the date, suitable for column header
    $twig->addFunction(new TwigFunction('getDate', function (\DateTime $weekOf, int $d) {
        $w = new \DateTime($weekOf->format('Y/m/d'));
        $w->add(new \DateInterval(sprintf('P%dD', $d)));

        return $w->format('M j');
    }));

    $weekOf = new \DateTime('now');
    $weekOf = getMonday($weekOf);
    $response->getBody()->write($twig->render('attendance.html.twig', [
        'classrooms' => ClassroomQuery::create()->find(),
        'weekOf'     => $weekOf
    ]));

    return $response;
});


$app->get('/enrollment', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $loader = new \Twig_Loader_Filesystem('../templates');
    $twig   = new \Twig_Environment($loader, array(
        'cache' => false
    ));

    $response->getBody()->write($twig->render('enrollment.html.twig', []));

    return $response;
});

$app->get('/classrooms', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $loader = new \Twig_Loader_Filesystem('../templates');
    $twig   = new \Twig_Environment($loader, array(
        'cache' => false
    ));

    $response->getBody()->write($twig->render('classrooms.html.twig', []));

    return $response;
});

////////////////////////////////////////////////////////////////////////////////////////////////////
// Routing for API
////////////////////////////////////////////////////////////////////////////////////////////////////

// Classrooms
$app->get('/api/classrooms/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        $resource = $engine->getClassroomById($args[ 'id' ]);
        if (null === $resource) {
            return $response->withStatus(404, 'Not Found');
        }
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($resource));

        return $response;
    });

$app->get('/api/classrooms',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        $results = $engine->getClassrooms();

        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->post('/api/classrooms',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        $results = $engine->postClassroom($request->getParsedBody());
        if (null === $results) {
            return $response->withStatus(404, 'Not Found');
        }

        $response = $response->withStatus(201, 'Created');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->put('/api/classrooms/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        $results = $engine->putClassroomById($args[ 'id' ], $request->getParsedBody());
        if (null === $results) {
            return $response->withStatus(404, 'Not Found');
        }
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->delete('/api/classrooms/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        if ( ! $engine->deleteClassroomById($args[ 'id' ])) {
            $response = $response->withStatus(404, 'Not Found');

            return $response;
        }
        $response = $response->withStatus(204, 'No Content');
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    });


// Students
$app->get('/api/students/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        $results = $engine->getStudentById($args[ 'id' ]);
        if (null === $results) {
            return $response->withStatus(404, 'Not Found');
        }
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->get('/api/students',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        $results  = $engine->getStudents();
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->post('/api/students',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        $id       = $engine->postStudent($request->getParsedBody());
        $response = $response->withStatus(201, 'Created');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($id));

        return $response;
    });

$app->put('/api/students/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        if ( ! $engine->putStudentById($args[ 'id' ], $request->getParsedBody())) {
            return $response->withStatus(404, 'Not Found');
        }
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($args[ 'id' ]));

        return $response;
    });

$app->delete('/api/students/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        if ( ! $engine->deleteStudentById($args[ 'id' ])) {
            $response = $response->withStatus(404, 'Not Found');

            return $response;
        }
        $response = $response->withStatus(204, 'No Content');
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    });


// Schedules
$app->get('/api/schedules/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        $results = $engine->getScheduleById($args[ 'id' ]);
        if (null === $results) {
            return $response->withStatus(404, 'Not Found');
        }
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->get('/api/schedules',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        $results  = $engine->getSchedules($request, $response, $args);
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->post('/api/schedules',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        $id       = $engine->postSchedule($request->getParsedBody());
        $response = $response->withStatus(201, 'Created');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($id));

        return $response;
    });

$app->put('/api/schedules/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        if ( ! $engine->putScheduleById($args[ 'id' ], $request->getParsedBody())) {
            return $response->withStatus(404, 'Not Found');
        }
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($args[ 'id' ]));

        return $response;
    });

$app->delete('/api/schedules/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        return $engine->deleteScheduleById($request, $response, $args);
    });


$app->run();
