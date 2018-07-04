<?php

class ProductCommentsService
{
	public function getAdminProductComments($inputs, $return_type='paginate', $limit = 20)
	{
		$product_comments = ProductComments::Select('product_comments.*','product.product_name', 'product.url_slug', 'product.product_code',
													'users.user_name','users.first_name','users.last_name')
											->leftJoin('product', 'product.id', '=', 'product_comments.product_id')
											->leftJoin('users', 'users.id', '=', 'product_comments.user_id');

		if(isset($inputs['product_id_from']) && $inputs['product_id_from'] > 0)
			$product_comments->where('product_id', '>=', $inputs['product_id_from']);
		if(isset($inputs['product_id_to']) && $inputs['product_id_to'] > 0)
			$product_comments->where('product_id', '<=', $inputs['product_id_to']);

		if(isset($inputs['comment_id_from']) && $inputs['comment_id_from'] > 0)
			$product_comments->where('product_comments.id', '>=', $inputs['comment_id_from']);
		if(isset($inputs['comment_id_to']) && $inputs['comment_id_to'] > 0)
			$product_comments->where('product_comments.id', '<=', $inputs['comment_id_to']);

		if(isset($inputs['product_title']) && $inputs['product_title'] != '')
			$product_comments->where('product.product_name', 'like', '%'.$inputs['product_title'].'%');

		if(isset($inputs['commented_by']) && $inputs['commented_by'] != '')
		{
			$commented_by = '%'.$inputs['commented_by'].'%';
			$product_comments->whereRaw('( users.user_name like ? OR users.first_name like ? OR users.last_name like ? )', array($commented_by,$commented_by,$commented_by));
		}
		$page = (Input::has('page') && Input::get('page')>0)?Input::get('page'):1;
		Paginator::setCurrentPage($page);

		if($return_type=='paginate')
			$product_comments = $product_comments->paginate($limit);
		else
			$product_comments = $product_comments->get($limit);

		return $product_comments;

	}
	public function updateComment($comment_id = null, $data = array())
	{
		if(is_null($comment_id) || empty($data))
			return false;
		try{
			ProductComments::where('id',$comment_id)->update($data);
			return true;
		}
		catch(Exception $e){
			return false;
		}
	}
	public function bulkDeleteComment($comment_ids){
		if(!empty($comment_ids))
		{
			try{
				ProductComments::whereIn('id', $comment_ids)->delete();
				return true;
			}
			catch(Exception $e)
			{
				return false;
			}
		}
		else
			return false;
	}
}