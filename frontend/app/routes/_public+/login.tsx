import { getFormProps, getInputProps, useForm } from '@conform-to/react';
import { getZodConstraint, parseWithZod } from '@conform-to/zod';
import { json, redirect, type ActionFunctionArgs, type LoaderFunctionArgs } from "@remix-run/node";
import { Form, useActionData } from '@remix-run/react';
import { z } from 'zod';
import { Field } from '~/components/forms';
import { Button } from '~/components/ui/button';
import { getOptionalUser } from "~/server/auth.server";
import { commitSession, getSession } from "~/server/session.server";
import { authenticateUser } from "~/server/auth.server";

export const loader = async ({ request }: LoaderFunctionArgs) => {
    const user = await getOptionalUser({ request });
    if (user) {
        return redirect('/');
    }
    return null;
};

const LoginSchema = z.object({
    email: z
        .string({ required_error: "L'email est obligatoire." })
        .email({ message: 'Cet email est invalide.' }),
    password: z.string({ required_error: 'Le mot de passe est obligatoire.' }),
});

export const action = async ({ request }: ActionFunctionArgs) => {
    const formData = await request.formData();
    const submission = await parseWithZod(formData, {
        schema: LoginSchema,
    });

    if (submission.status !== 'success') {
        return json({ result: submission.reply() }, { status: 400 });
    }

    const { email, password } = submission.value;
    const user = await authenticateUser(email, password);

    if (!user) {
        return json({ result: { errors: { email: ['Email ou mot de passe incorrect'] } } }, { status: 401 });
    }

    const session = await getSession(request);
    session.set("user", { id: user.id, email: user.email, name: user.name });

    return redirect("/profile", {
        headers: {
            "Set-Cookie": await commitSession(session),
        },
    });
};

export default function Login() {
    const actionData = useActionData<typeof action>();
    const [form, fields] = useForm({
        constraint: getZodConstraint(LoginSchema),
        onValidate({ formData }) {
            return parseWithZod(formData, { schema: LoginSchema });
        },
        lastResult: actionData?.result,
    });

    return (
        <div className='max-w-[600px] mx-auto'>
            <h1 className="text-2xl font-bold mb-6">Connexion</h1>
            <Form {...getFormProps(form)} method='POST' reloadDocument className='flex flex-col gap-4'>
                <Field
                    inputProps={getInputProps(fields.email, { type: 'email' })}
                    labelsProps={{ children: 'Adresse e-mail' }}
                    errors={fields.email.errors}
                />
                <Field
                    inputProps={getInputProps(fields.password, { type: 'password' })}
                    labelsProps={{ children: 'Mot de passe' }}
                    errors={fields.password.errors}
                />
                <Button className='ml-auto' type='submit'>
                    Se connecter
                </Button>
            </Form>
        </div>
    );
}
