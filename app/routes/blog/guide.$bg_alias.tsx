import { json, type LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card, CardContent } from "~/components/ui/card";
import { LazyImage } from "~/components/ui/image";
import { formatDate } from "~/lib/utils";
import invariant from "tiny-invariant";

export async function loader({ params }: LoaderFunctionArgs) {
  const { bg_alias } = params;
  invariant(bg_alias, "Alias de guide requis");

  const response = await fetch(
    `${process.env.API_URL}/blog/guides/${bg_alias}`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    if (response.status === 410) {
      throw new Response("Ce guide n'existe plus", { status: 410 });
    }
    throw new Response("Guide introuvable", { status: 404 });
  }

  return json(await response.json());
}

export default function GuidePage() {
  const { guide, relatedGuides } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold mb-2">{guide.title}</h1>
          <div className="flex gap-4 text-sm text-gray-500">
            <time>Publié le {formatDate(guide.createdAt)}</time>
            <time>Modifié le {formatDate(guide.updatedAt)}</time>
          </div>
        </div>

        {/* Content */}
        <Card className="overflow-hidden mb-12">
          {guide.image && (
            <div className="relative aspect-video">
              <LazyImage
                src={guide.image}
                alt={guide.title}
                className="object-cover"
                blur
              />
            </div>
          )}

          <CardContent className="prose max-w-none p-6 lg:p-8">
            {/* Table des matières */}
            {guide.sections?.length > 0 && (
              <div className="mb-8 p-4 bg-gray-50 rounded-lg">
                <h2 className="text-lg font-semibold mb-4">
                  Table des matières
                </h2>
                <nav className="space-y-2">
                  {guide.sections.map((section) => (
                    <div key={section.id}>
                      <a 
                        href={`#${section.alias}`}
                        className="text-blue-600 hover:underline block"
                      >
                        {section.title}
                      </a>
                      {section.subsections?.map((sub) => (
                        <a
                          key={sub.id}
                          href={`#${sub.alias}`}
                          className="text-gray-600 hover:underline block pl-4 text-sm"
                        >
                          {sub.title}
                        </a>
                      ))}
                    </div>
                  ))}
                </nav>
              </div>
            )}

            <div 
              className="mt-8"
              dangerouslySetInnerHTML={{ __html: guide.content }} 
            />
          </CardContent>
        </Card>

        {/* Articles liés */}
        {relatedGuides?.length > 0 && (
          <div className="space-y-8">
            <h2 className="text-2xl font-semibold">
              Autres guides qui pourraient vous intéresser
            </h2>
            <div className="grid sm:grid-cols-2 gap-6">
              {relatedGuides.map((related) => (
                <Card key={related.id} className="hover:shadow-lg transition">
                  <div className="relative aspect-video">
                    <LazyImage
                      src={related.image}
                      alt={related.title}
                      className="object-cover rounded-t-lg"
                      blur
                    />
                  </div>
                  <CardContent className="p-6">
                    <h3 className="font-semibold mb-2">
                      {related.title}
                    </h3>
                    <p className="text-sm text-gray-600 mb-4">
                      {related.preview}
                    </p>
                    <a 
                      href={`/blog/guide/${related.alias}`}
                      className="text-blue-600 hover:underline text-sm"
                    >
                      Lire le guide →
                    </a>
                  </CardContent>
                </Card>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
