import { ActionFunctionArgs, json } from "@remix-run/node";
import { Form, useActionData } from "@remix-run/react";
import { useState } from "react";
import { requireUserId } from "~/utils/session.server";

export async function action({ request }: ActionFunctionArgs) {
  const userId = await requireUserId(request);
  const formData = await request.formData();
  const file = formData.get('file') as File;

  if (!file) {
    return json({ error: "No file provided" }, { status: 400 });
  }

  const response = await fetch(`${process.env.API_URL}/upload/image`, {
    method: 'POST',
    body: formData
  });

  if (!response.ok) {
    return json({ error: "Upload failed" }, { status: 500 });
  }

  return json(await response.json());
}

export default function UploadPage() {
  const [preview, setPreview] = useState<string>();
  const actionData = useActionData<typeof action>();

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setPreview(reader.result as string);
      };
      reader.readAsDataURL(file);
    }
  };

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-xl mx-auto">
        <h1 className="text-3xl font-bold mb-8">
          Upload d'Images
        </h1>

        <Form method="post" encType="multipart/form-data" className="space-y-6">
          <div className="border-2 border-dashed border-gray-300 rounded-lg p-6">
            <input
              type="file"
              name="file"
              accept="image/*"
              onChange={handleFileChange}
              className="w-full"
            />

            {preview && (
              <div className="mt-4">
                <img
                  src={preview}
                  alt="Preview"
                  className="max-w-full h-auto rounded"
                />
              </div>
            )}
          </div>

          {actionData?.error && (
            <div className="text-red-600">
              {actionData.error}
            </div>
          )}

          <button
            type="submit"
            className="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
          >
            Upload
          </button>
        </Form>
      </div>
    </div>
  );
}
