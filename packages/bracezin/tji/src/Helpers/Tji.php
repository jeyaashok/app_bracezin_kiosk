<?php

namespace Tji\Helpers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use View;

class Tji
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * Ledger constructor.
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public static function getDefaultParamForWeb($input)
    {
        $input['array'] = false;
        $input['paginate'] = (Arr::has($input, 'paginate')) ? $input['paginate'] : 10;
        $input['search'] = (Arr::has($input, 'search')) ? $input['search'] : '';
        $input['order'] = (Arr::has($input, 'order')) ? $input['order'] : 'created_at|desc,updated_at|desc';

        return $input;
    }

    public static function getRequestType($request)
    {
        $type = 'api';
        if ($request->ajax() && ! $request->is('api/*')) {
            $type = 'ajax';
        }
        if (! $request->ajax() && ! $request->is('api/*')) {
            $type = 'web';
        }

        return $type;
    }

    public static function indexResponse($request = null, $input = [])
    {
        $search = Arr::has($request->all(), 'search') ? $request->all()['search'] : null;
        $requsestType = 'api';
        if ($request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'ajax';
        }
        if (! $request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'web';
        }

        $data = Arr::has($input, 'data') ? $input['data'] : null;
        $message = Arr::has($input, 'message') ? $input['message'] : null;
        $webUrl = Arr::has($input, 'webUrl') ? $input['webUrl'] : null; // call blade file (eg: package::resource.index)
        $ajaxUrl = Arr::has($input, 'ajaxUrl') ? $input['ajaxUrl'] : null; // call blade file (eg: package::resource.index)
        $responseData = null;

        switch ($requsestType) {
            case 'ajax':
                $view = View::make($ajaxUrl, [
                    'items' => $data,
                    'search' => $search])->render();
                $responseData = response()->json(compact('view'));
                break;
            case 'web':
                $view = view($webUrl, [
                    'items' => $data,
                    'search' => $search])->render();
                $responseData = $view;
                break;
            default:
                $responseData = response()->json([
                    'data' => $data,
                    'message' => 'success',
                    'code' => Response::HTTP_OK,
                    'response' => Response::$statusTexts[Response::HTTP_OK]], Response::HTTP_OK);
                break;
        }

        return $responseData;
    }

    public static function showResponse($request = null, $input = [])
    {
        $requsestType = 'api';
        if ($request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'ajax';
        }
        if (! $request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'web';
        }

        $data = Arr::has($input, 'data') ? $input['data'] : null;
        $webUrl = Arr::has($input, 'webUrl') ? $input['webUrl'] : null; // call blade file (eg: package::resource.index)
        $ajaxUrl = Arr::has($input, 'ajaxUrl') ? $input['ajaxUrl'] : null; // call blade file (eg: package::resource.index)
        $responseData = null;

        switch ($requsestType) {
            case 'ajax':
                $view = View::make($ajaxUrl, ['item' => $data])->render();
                $responseData = response()->json(compact('view'));
                break;
            case 'web':
                $view = view($webUrl, ['item' => $data])->render();
                $responseData = $view;
                break;
            default:
                $responseData = response()->json([
                    'data' => $data,
                    'message' => 'success',
                    'code' => Response::HTTP_OK,
                    'response' => Response::$statusTexts[Response::HTTP_OK]], Response::HTTP_OK);
                break;
        }

        return $responseData;
    }

    public static function storeResponse($request = null, $input = [])
    {
        $requsestType = 'api';
        if ($request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'ajax';
        }
        if (! $request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'web';
        }

        $data = Arr::has($input, 'data') ? $input['data'] : null;
        $redirectTo = Arr::has($input, 'redirectTo') ? $input['redirectTo'] : null; // call blade file (eg: package::resource.store)
        $message = Arr::has($input, 'message') ? $input['message'] : 'New Record Created Successfully';

        $responseData = null;
        switch ($requsestType) {
            case 'ajax':
                $responseData = redirect($redirectTo)->withStatus(__($message));
                break;
            case 'web':
                $responseData = redirect($redirectTo)->withStatus(__($message));
                break;
            default:
                $responseData = response()->json([
                    'data' => $data,
                    'message' => $message,
                    'code' => Response::HTTP_CREATED,
                    'response' => Response::$statusTexts[Response::HTTP_CREATED]], Response::HTTP_CREATED);
                break;
        }

        return $responseData;
    }

    public static function updateResponse($request = null, $input = [])
    {
        $requsestType = 'api';
        if ($request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'ajax';
        }
        if (! $request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'web';
        }

        $data = Arr::has($input, 'data') ? $input['data'] : null;
        $redirectTo = Arr::has($input, 'redirectTo') ? $input['redirectTo'] : null; // call blade file (eg: package::resource.store)
        $message = Arr::has($input, 'message') ? $input['message'] : 'Updated Record Successfully';

        $responseData = null;
        switch ($requsestType) {
            case 'ajax':
                $responseData = redirect($redirectTo)->withStatus(__($message));
                break;
            case 'web':
                $responseData = redirect($redirectTo)->withStatus(__($message));
                break;
            default:
                $responseData = response()->json([
                    'data' => $data,
                    'message' => $message,
                    'code' => Response::HTTP_OK,
                    'response' => Response::$statusTexts[Response::HTTP_OK]], Response::HTTP_OK);
                break;
        }

        return $responseData;
    }

    public static function deleteResponse($request = null, $input = [])
    {
        $requsestType = 'api';
        if ($request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'ajax';
        }
        if (! $request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'web';
        }

        $data = Arr::has($input, 'data') ? $input['data'] : null;
        $redirectTo = Arr::has($input, 'redirectTo') ? $input['redirectTo'] : null; // call blade file (eg: package::resource.store)
        $message = Arr::has($input, 'message') ? $input['message'] : 'Updated Record Successfully';

        $responseData = null;
        switch ($requsestType) {
            case 'ajax':
                $responseData = redirect($redirectTo)->withStatus(__($message));
                break;
            case 'web':
                $responseData = redirect($redirectTo)->withStatus(__($message));
                break;
            default:
                $responseData = response()->json([
                    'data' => $data,
                    'message' => $message,
                    'code' => Response::HTTP_OK,
                    'response' => Response::$statusTexts[Response::HTTP_OK]], Response::HTTP_OK);
                break;
        }

        return $responseData;
    }

    public static function successMessage($message = null)
    {
        $responseData = response()->json([
            'message' => $message,
            'code' => Response::HTTP_OK,
            'response' => Response::$statusTexts[Response::HTTP_OK]], Response::HTTP_OK);

        return $responseData;
    }

    public static function successResponse(?Request $request = null, $data = null, $input = [])
    {
        $requsestType = 'api';
        if ($request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'ajax';
        }
        if (! $request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'web';
        }

        $message = Arr::has($input, 'message') ? $input['message'] : 'success';
        $code = 200;
        $webUrl = Arr::has($input, 'webUrl') ? $input['webUrl'] : null; // call blade file (eg: package::resource.index)
        $ajaxUrl = Arr::has($input, 'ajaxUrl') ? $input['ajaxUrl'] : null; // call blade file (eg: package::resource.index)
        $redirectUrl = Arr::has($input, 'redirectUrl') ? $input['redirectUrl'] : null; // call blade file (eg: package::resource.index)

        if (! ($webUrl || $ajaxUrl || $redirectUrl)) {
            $requsestType = 'api';
        }
        $responseData = null;
        switch ($requsestType) {
            case 'ajax':
                if ($redirectUrl) {
                    $responseData = redirect($redirectUrl)->with('data', $data);
                } elseif ($ajaxUrl) {
                    $view = View::make($ajaxUrl)->with('data', $data)->render();
                    $responseData = response()->json(compact('view'));
                }
                break;
            case 'web':
                if ($redirectUrl) {
                    $responseData = redirect($redirectUrl)->with('data', $data);
                } elseif ($webUrl) {
                    $view = View::make($webUrl)->with('data', $data)->render();
                    $responseData = response()->json(compact('view'));
                }
                break;
            default:
                $responseData = response()->json([
                    'data' => $data,
                    'message' => $message,
                    'code' => $code,
                    'response' => Response::$statusTexts[Response::HTTP_OK]], $code);
                break;
        }

        return $responseData;
    }

    public static function errorResponse($request = null, ?\Exception $e = null, $input = [])
    {
        if (is_null($e)) {
            $e = new \Exception('Something went wrong!', Response::HTTP_METHOD_NOT_ALLOWED);
        }
        $requsestType = 'api';
        if ($request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'ajax';
        }
        if (! $request->ajax() && ! $request->is('api/*')) {
            $requsestType = 'web';
        }

        $error = ($e->getMessage()) ? $e->getMessage() : 'Something went wrong!';
        $errorCode = ($e->getCode()) ? $e->getCode() : 405;
        $errorResponse = ($errorCode && is_numeric($errorCode)) ? Response::$statusTexts[$errorCode] : $errorCode;
        $errorCode = ($errorCode && is_numeric($errorCode)) ? (int) $errorCode : 405;
        $webUrl = Arr::has($input, 'webUrl') ? $input['webUrl'] : null; // call blade file (eg: package::resource.index)
        $ajaxUrl = Arr::has($input, 'ajaxUrl') ? $input['ajaxUrl'] : null; // call blade file (eg: package::resource.index)
        $redirectUrl = Arr::has($input, 'redirectUrl') ? $input['redirectUrl'] : null; // call blade file (eg: package::resource.index)

        if (! ($webUrl || $ajaxUrl || $redirectUrl)) {
            $requsestType = 'api';
        }
        $responseData = null;
        switch ($requsestType) {
            case 'ajax':
                if ($redirectUrl) {
                    $responseData = redirect($redirectUrl)->withStatus(__($error));
                } elseif ($ajaxUrl) {
                    $view = View::make($ajaxUrl)->render();
                    $responseData = response()->json(compact('view'));
                }
                break;
            case 'web':
                if ($redirectUrl) {
                    $responseData = redirect($redirectUrl)->withStatus(__($error));
                } elseif ($webUrl) {
                    $view = View::make($webUrl)->render();
                    $responseData = response()->json(compact('view'));
                }
                break;
            default:
                $responseData = response()->json([
                    'error' => $error,
                    'message' => $error,
                    'code' => $errorCode,
                    'factor' => $input,
                    'response' => $errorResponse], $errorCode);
                break;
        }

        return $responseData;
    }

    public static function getDefaultParamForDataTable($request = null)
    {
        $input = ($request) ? $request->all() : [];
        $draw = Arr::has($input, 'draw') ? intval($input['draw']) : null;
        $paginate = Arr::has($input, 'length') ? intval($input['length']) : 15;
        $page = (Arr::has($input, 'length') && Arr::has($input, 'start')) ? intval(($input['start'] / $input['length']) + 1) : 1;
        $search = (Arr::has($input, 'search') && Arr::has($input['search'], 'value')) ? $input['search']['value'] : null;

        $columns = [];
        if (Arr::has($input, 'columns') && count($input['columns']) > 0) {
            for ($i = 0; $i < count($input['columns']); $i++) {
                $columns[$i] = $input['columns'][$i]['data'];
            }
        }

        $orders = '';
        if (Arr::has($input, 'order') && count($input['order']) > 0) {
            $columnArray = (Arr::has($input, 'columns') && count($input['columns']) > 0) ? $input['columns'] : [];
            for ($i = 0; $i < count($input['order']); $i++) {
                $order = $input['order'][$i];
                $orderValue = $columnArray[intval($order['column'])]['data'];
                $orderDirection = $order['dir'];
                $orders = ($orders && $orders != '') ? $orders.',' : $orders;
                $orders = $orders.$orderValue.'|'.$orderDirection;
            }
        }

        $defaultParam = [
            'draw' => $draw,
            'array' => false,
            'paginate' => $paginate,
            'page' => $page,
            'search' => $search,
            'order' => $orders,
            'columns' => $columns,
        ];

        return $defaultParam;
    }

    public static function DataTableMapView($input, $items)
    {
        $data = [];
        $unique = Arr::has($input, 'unique') ? $input['unique'] : null;
        $draw = Arr::has($input, 'draw') ? $input['draw'] : null;
        $recordsTotal = $items->count();
        $recordsFiltered = ($unique) ? $items->count() : $items->total();
        $location = Arr::has($input, 'viewLocation') ? $input['viewLocation'] : null;
        $columns = Arr::has($input, 'columns') ? $input['columns'] : [];

        if ($items && $items->count() > 0 && $columns && count($columns) > 0) {
            foreach ($items as $item) {
                for ($i = 0; $i < count($columns); $i++) {
                    $nestedData[$columns[$i]] = ($location) ?
                    View::make($location.'.dataTable')
                        ->with('item', $item)
                        ->with('column', $columns[$i])
                        ->with('input', $input)
                        ->render() : '';
                }
                $data[] = $nestedData;
            }
        }
        $json_data = [
            'draw' => intval($draw),
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsFiltered),
            'data' => $data,
        ];

        return json_encode($json_data);
    }

    public function closeResponse()
    {
        if (ob_get_contents()) {
            ob_end_clean();
        }
        if (ob_get_length()) {
            ob_end_clean();
        }
        header("Connection: close\r\n");
        header("Content-Encoding: none\r\n");
        ignore_user_abort(true);
        ob_start();
        // echo "ok";
        Response::HTTP_OK;
        // fastcgi_finish_request();
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();
        flush();
        if (ob_get_contents()) {
            ob_end_clean();
        }
        if (ob_get_length()) {
            ob_end_clean();
        }
        sleep(1);
    }

    public function arrayUnique($array)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            if (! in_array($value, $newArray)) {
                array_push($newArray, $array[$key]);
            }
        }

        return $newArray;
    }
}
