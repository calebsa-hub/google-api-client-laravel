<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Classroom;
use Google\Service\Classroom\Course;
use Google\Service\Exception;

use Illuminate\Http\Request;

class GoogleClassroomController extends Controller
{
    public function createCourse(Request $request)
    {
        // Inicialize o cliente do Google
        $client = new Client();
        $client->setAuthConfig(env('GOOGLE_CLASSROOM_CREDENTIALS_PATH'));
        $client->setAccessType('offline');

        // Autentique o cliente
        if ($client->getAccessToken()) {
            // Configure o serviço do Google Classroom
            $service = new Classroom($client);

            // Dados do curso (substitua com seus próprios dados)
            $curso = new Course([
                'name' => 'Nome do Curso',
                'section' => 'Seção do Curso',
                'descriptionHeading' => 'Descrição do Curso',
                'description' => 'Esta é a descrição do curso.',
                'room' => 'Sala de Aula',
            ]);

            // Crie o curso
            $cursoCriado = $service->courses->create($curso);

            // Redirecione de volta com uma mensagem de sucesso
            return redirect()->route('boas-vindas')->with('success', 'Curso criado com sucesso.');
        } else {
            // Redirecione de volta com uma mensagem de erro
            return redirect()->route('boas-vindas')->with('error', 'Erro na autenticação com o Google Classroom.');
        }
    }

    public function getCourse(Request $request, $id)
    {
        $client = new Client();
        $client->setAuthConfig(env('GOOGLE_CLASSROOM_CREDENTIALS_PATH'));
        $client->setAccessType('offline');

        // Autentique o cliente
        if ($client->getAccessToken()) {
            // Configure o serviço do Google Classroom
            $service = new Classroom($client);

            // Faça a solicitação para obter os detalhes do curso pelo ID
            $curso = $service->courses->get($id);

            // Retorne os detalhes do curso para a visualização
            return view('detalhes-curso', compact('curso'));
        } else {
            // Redirecione de volta com uma mensagem de erro
            return redirect()->route('boas-vindas')->with('error', 'Erro na autenticação com o Google Classroom.');
        }
    }

    public function listCourses()
    {
        $client = new Client();
        $client->setAuthConfig(env('GOOGLE_CLASSROOM_CREDENTIALS_PATH'));
        $client->setAccessType('offline');
        $service = new Classroom($client);

        $courses = [];
        $pageToken = '';

        do {
            $params = [
                'pageSize' => 100,
                'pageToken' => $pageToken
            ];
            $response = $service->courses->listCourses($params);
            $courses = array_merge($courses, $response->courses);
            $pageToken = $response->nextPageToken;
        } while (!empty($pageToken));

        if (count($courses) == 0) {
            print "No courses found.\n";
        } else {
            print "Courses:\n";
            foreach ($courses as $course) {
                printf("%s (%s)\n", $course->name, $course->id);
            }
        }
        return $courses;
    }

    public function updateCourse(Request $request, $id)
    {
        $client = new Client();
        $client->setAuthConfig(env('GOOGLE_CLASSROOM_CREDENTIALS_PATH'));
        $client->setAccessType('offline');
        $service = new Classroom($client);

        $course = $service->courses->get($id);
        $course->section = 'Period 3';
        $course->room = '302';
        $course = $service->courses->update($id, $course);
        printf("Course '%s' updated.\n", $course->name);

        return $course;
    }

    public function patchCourse(Request $request, $id)
    {
        $client = new Client();
        $client->setAuthConfig(env('GOOGLE_CLASSROOM_CREDENTIALS_PATH'));
        $client->setAccessType('offline');
        $service = new Classroom($client);

        try {
            $course = new Course([
                'section' => 'Period 3',
                'room' => '302'
            ]);
            $params = ['updateMask' => 'section,room'];
            $course = $service->courses->patch($id, $course, $params);
            printf("Course '%s' updated.\n", $course->name);
            return $course;
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }

    }

    public function deleteCourse(Request $request, $id)
    {
        $client = new Client();
        $client->setAuthConfig(env('GOOGLE_CLASSROOM_CREDENTIALS_PATH'));
        $client->setAccessType('offline');

        // Autentique o cliente
        if ($client->getAccessToken()) {
            // Configure o serviço do Google Classroom
            $service = Classroom($client);

            try {
                // Exclua o curso pelo ID
                $service->courses->delete($id);

                // Redirecione de volta com uma mensagem de sucesso
                return redirect()->route('boas-vindas')->with('success', 'Curso excluído com sucesso.');
            } catch (Exception $e) {
                // Lidar com erros da API do Google Classroom
                return redirect()->route('boas-vindas')->with('error', 'Erro ao excluir o curso: ' . $e->getMessage());
            }
        } else {
            // Redirecione de volta com uma mensagem de erro
            return redirect()->route('boas-vindas')->with('error', 'Erro na autenticação com o Google Classroom.');
        }
    }

    public function createCourseWithAlias(Request $request, $cursoId, $alias)
    {
        $client = new Client();
        $client->setAuthConfig(env('GOOGLE_CLASSROOM_CREDENTIALS_PATH'));
        $client->setAccessType('offline');

        if ($client->getAccessToken()) {
            // Configure o serviço do Google Classroom
            $service = new Classroom($client);

            // Dados do alias
            $dadosAlias = new Google_Service_Classroom_CourseAlias([
                'alias' => $alias,
            ]);

            // Crie o alias
            $aliasCriado = $service->courses_aliases->create($cursoId, $dadosAlias);

            // Redirecione de volta com uma mensagem de sucesso
            return redirect()->route('boas-vindas')->with('success', 'Alias criado com sucesso.');
        } else {
            // Redirecione de volta com uma mensagem de erro
            return redirect()->route('boas-vindas')->with('error', 'Erro na autenticação com o Google Classroom.');
        }

    }

    public function addAliasToCourse(Request $request, $cursoId, $alias)
    {
        $client = new Client();
        $client->setAuthConfig(env('GOOGLE_CLASSROOM_CREDENTIALS_PATH'));
        $client->setAccessType('offline');

        // Autentique o cliente
        if ($client->getAccessToken()) {
            // Configure o serviço do Google Classroom
            $service = new Classroom($client);

            // Dados do alias
            $dadosAlias = new Google_Service_Classroom_CourseAlias([
                'alias' => $alias,
            ]);

            // Adicione o alias ao curso
            $service->courses_aliases->create($cursoId, $dadosAlias);

            // Redirecione de volta com uma mensagem de sucesso
            return redirect()->route('boas-vindas')->with('success', 'Alias adicionado com sucesso ao curso.');
        } else {
            // Redirecione de volta com uma mensagem de erro
            return redirect()->route('boas-vindas')->with('error', 'Erro na autenticação com o Google Classroom.');
        }
    }

    public function listCoursesAliases(Request $request)
    {
        $client = new Client();
        $client->setAuthConfig(env('GOOGLE_CLASSROOM_CREDENTIALS_PATH'));
        $client->setAccessType('offline');

        // Autentique o cliente
        if ($client->getAccessToken()) {
            // Configure o serviço do Google Classroom
            $service = new Classroom($client);

            // Faça a solicitação para listar os aliases dos cursos
            $aliases = $service->courses_aliases->listCoursesAliases();

            // Retorne a lista de aliases para a visualização
            return view('lista-aliases', compact('aliases'));
        } else {
            // Redirecione de volta com uma mensagem de erro
            return redirect()->route('boas-vindas')->with('error', 'Erro na autenticação com o Google Classroom.');
        }
    }

    public function deleteCourseAlias(Request $request, $cursoId, $alias)
    {
        $client = new Client();
        $client->setAuthConfig(env('GOOGLE_CLASSROOM_CREDENTIALS_PATH'));
        $client->setAccessType('offline');

        // Autentique o cliente
        if ($client->getAccessToken()) {
            // Configure o serviço do Google Classroom
            $service = new Classroom($client);

            // Faça a solicitação para excluir o alias
            $service->courses_aliases->delete($cursoId, $alias);

            // Redirecione de volta com uma mensagem de sucesso
            return redirect()->route('boas-vindas')->with('success', 'Alias excluído com sucesso.');
        } else {
            // Redirecione de volta com uma mensagem de erro
            return redirect()->route('boas-vindas')->with('error', 'Erro na autenticação com o Google Classroom.');
        }
    }

    // public function createInvitation()
    // {

    // }

    // public function getInvitation()
    // {

    // }

    // public function acceptInvitation()
    // {

    // }

    // public function deleteInvitation()
    // {

    // }
}
