<?php

namespace  App\Models\Back\Catalog\Attributes;

use App\Models\Back\Settings\Category;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Attributes extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'attributes';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var Request
     */
    protected $request;


    /**
     * Validate new category Request.
     *
     * @param Request $request
     *
     * @return $this
     */
    public function validateRequest(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $this->request = $request;

        return $this;
    }


    /**
     * Store new category.
     *
     * @return false
     */
    public function create()
    {
        $id = $this->insertGetId([
            'group'       => '',
            'title'       => $this->request->input('title'),
            'type'        => $this->setType(),
            'sort_order'  => 0,
            'status'      => (isset($this->request->status) and $this->request->status == 'on') ? 1 : 0,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now()
        ]);
        
        if ($id) {
            return $this->find($id);
        }

        return false;
    }


    /**
     * @param Category $category
     *
     * @return false
     */
    public function edit()
    {
        $saved = $this->update([
            'group'       => '',
            'title'       => $this->request->input('title'),
            'type'        => $this->setType(),
            'sort_order'  => 0,
            'status'      => (isset($this->request->status) and $this->request->status == 'on') ? 1 : 0,
            'updated_at'  => Carbon::now()
        ]);
        
        if ($saved) {
            return $this;
        }
        
        return false;
    }
    
    
    /**
     * @return array
     */
    public function getList()
    {
        $response = [];
        $values = Attributes::query()->get();

        foreach ($values as $value) {
            $response[$value->group]['group'] = $value->translation->group_title;
            $response[$value->group]['items'][] = [
                'id' => $value->id,
                'title' => $value->translation->title,
                'sort_order' => $value->sort_order
            ];
        }

        return $response;
    }


    /**
     * @param string $type
     *
     * @return mixed
     */
    private function setType(string $type = 'text')
    {
        return $this->request->get('type', $type);
    }
}
