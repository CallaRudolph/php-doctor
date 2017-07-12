<?php
  date_default_timezone_set('America/Los_Angeles');
  require_once __DIR__."/../vendor/autoload.php";
  require_once __DIR__."/../src/Patient.php";
  require_once __DIR__."/../src/Doctor.php";

  $app = new Silex\Application();

    $server = 'mysql:host=localhost:8889;dbname=doctor';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' =>__DIR__.'/../views'
    ));

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();


    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig', array('doctors' => Doctor::getAll()));
    });

    $app->get("/patients", function() use ($app) {
        return $app['twig']->render('patients.html.twig', array('patients' => Patient::getAll()));
    });

    $app->post("/patients", function() use ($app) {
        $patient_name = $_POST['name'];
        $dob = $_POST['dob'];
        $doctor_id = $_POST['doctor_id'];
        $patient = new Patient($patient_name, $dob, $doctor_id, $id = null);
        $patient->save();
        $doctor = Doctor::find($doctor_id);
        return $app['twig']->render('doctor.html.twig', array('doctor' => $doctor, 'patients' => $doctor->getPatients()));
    });

    $app->get("/doctors", function() use ($app) {
        return $app['twig']->render('doctors.html.twig', array('doctors' => Doctor::getAll()));
    });

    $app->get("/doctors/{id}", function($id) use ($app) {
        $doctor = Doctor::find($id);
        return $app['twig']->render('doctor.html.twig', array('doctor' => $doctor, 'patients' => $doctor->getPatients()));
    });

    $app->post("/doctors", function() use ($app) {
        $name = $_POST['name'];
        $specialty = $_POST['specialty'];
        $doctor = new Doctor($name, $specialty);
        $doctor->save();
        return $app['twig']->render('doctors.html.twig', array('doctors' => Doctor::getAll()));
    });

    $app->get("/doctors/{id}/edit", function($id) use ($app) {
        $doctor = Doctor::find($id);
        return $app['twig']->render('doctor_edit.html.twig', array('doctor' => $doctor));
    });

////how to update specialty???////
    $app->patch("/doctors/{id}", function($id) use ($app) {
        $name = $_POST['name'];
        $specialty = $_POST['specialty'];
        $doctor = Doctor::find($id);
        $doctor->update($name);
        $doctor->updateSpecialty($specialty);
        return $app['twig']->render('doctor.html.twig', array('doctor' => $doctor, 'patients' => $doctor->getPatients()));
    });

    $app->post("/delete_doctors", function() use ($app) {
        Doctor::deleteAll();
        return $app['twig']->render('index.html.twig');
    });

    $app->post("/delete_patients", function() use ($app) {
        Patient::deleteAll();
        return $app['twig']->render('index.html.twig');
    });

    $app->delete("/doctors/{id}", function($id) use ($app) {
        $doctors = Doctor::find($id);
        $doctors->delete();
        return $app['twig']->render('index.html.twig', array('doctors' => Doctor::getAll()));
    });

    return $app;
?>
