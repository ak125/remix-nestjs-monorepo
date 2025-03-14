import { LazyImage } from "~/components/ui/image";
import { formatDate } from "~/lib/utils";
import { Card } from "~/components/ui/card";

interface Article {
  id: string;
  title: string;
  alias: string;
  image: string;
  category: {
    name: string;
    alias: string;
  };
  updatedAt: string;
}

interface Props {
  articles: Article[];
  className?: string;
}

export function MostReadArticles({ articles, className }: Props) {
  return (
    <div className={className}>
      <Card className="p-6">
        <h2 className="text-xl font-bold mb-6">
          Articles les plus lus
        </h2>

        <div className="space-y-4">
          {articles.map((article) => (
            <div key={article.id} className="flex gap-4">
              <div className="w-24 h-24 shrink-0">
                <LazyImage
                  src={article.image}
                  alt={article.title}
                  className="w-full h-full object-cover rounded-lg"
                  blur
                />
              </div>

              <div className="space-y-1">
                <a 
                  href={`/blog/${article.category.alias}/${article.alias}`}
                  className="text-sm font-medium hover:text-blue-600 line-clamp-2"
                >
                  {article.title}
                </a>

                <div className="text-xs text-gray-500 space-y-0.5">
                  <p>{article.category.name}</p>
                  <time>
                    {formatDate(article.updatedAt)}
                  </time>
                </div>
              </div>
            </div>
          ))}
        </div>
      </Card>
    </div>
  );
}
