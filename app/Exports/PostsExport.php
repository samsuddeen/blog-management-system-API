<?php

namespace App\Exports;

use App\Models\Post;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PostsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Post::with(['category', 'author']);
    }

    public function headings(): array
    {
        return ['ID', 'Title', 'Body', 'Slug', 'Category', 'Author', 'Created At'];
    }

    public function map($post): array
{
    return [
        $post->id,
        $post->title,
        $post->body,
        $post->slug,
        $post->category->name ?? '',
        $post->author->name ?? '',
        $post->created_at ? $post->created_at->format('Y-m-d H:i:s') : '',
    ];
}

}
