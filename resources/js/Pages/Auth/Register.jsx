import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        account_type: 'individual',
        organization_name: '',
        organization_type: '',
        organization_website: '',
    });

    const submit = (e) => {
        e.preventDefault();

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Register" />

            <form onSubmit={submit}>
                <div>
                    <InputLabel htmlFor="account_type" value="Account Type" />
                    <div className="mt-2 flex items-center space-x-6">
                        <label className="flex items-center">
                            <input
                                type="radio"
                                name="account_type"
                                value="individual"
                                checked={data.account_type === 'individual'}
                                onChange={(e) => setData('account_type', e.target.value)}
                                className="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            />
                            <span className="ml-2 text-sm text-gray-700">Individual Attendee</span>
                        </label>
                        <label className="flex items-center">
                            <input
                                type="radio"
                                name="account_type"
                                value="organization"
                                checked={data.account_type === 'organization'}
                                onChange={(e) => setData('account_type', e.target.value)}
                                className="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            />
                            <span className="ml-2 text-sm text-gray-700">Organization / Event Planner</span>
                        </label>
                    </div>
                    <InputError message={errors.account_type} className="mt-2" />
                </div>

                {data.account_type === 'organization' && (
                    <div className="mt-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <h3 className="text-lg font-medium text-gray-900 mb-4">Organization Details</h3>
                        <div>
                            <InputLabel htmlFor="organization_name" value="Organization Name" />
                            <TextInput
                                id="organization_name"
                                name="organization_name"
                                value={data.organization_name}
                                className="mt-1 block w-full"
                                onChange={(e) => setData('organization_name', e.target.value)}
                                required={data.account_type === 'organization'}
                            />
                            <InputError message={errors.organization_name} className="mt-2" />
                        </div>

                        <div className="mt-4">
                            <InputLabel htmlFor="organization_type" value="Organization Type" />
                            <select
                                id="organization_type"
                                name="organization_type"
                                value={data.organization_type}
                                className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                onChange={(e) => setData('organization_type', e.target.value)}
                                required={data.account_type === 'organization'}
                            >
                                <option value="" disabled>Select Type</option>
                                <option value="Corporate">Corporate</option>
                                <option value="NGO">NGO</option>
                                <option value="Church">Church / Religious</option>
                                <option value="University">University / Education</option>
                                <option value="Community">Community Group</option>
                                <option value="Other">Other</option>
                            </select>
                            <InputError message={errors.organization_type} className="mt-2" />
                        </div>

                        <div className="mt-4">
                            <InputLabel htmlFor="organization_website" value="Website URL (Optional)" />
                            <TextInput
                                id="organization_website"
                                type="url"
                                name="organization_website"
                                value={data.organization_website}
                                className="mt-1 block w-full"
                                onChange={(e) => setData('organization_website', e.target.value)}
                            />
                            <InputError message={errors.organization_website} className="mt-2" />
                        </div>
                    </div>
                )}

                <div className="mt-4">
                    <InputLabel htmlFor="name" value="Your Full Name" />

                    <TextInput
                        id="name"
                        name="name"
                        value={data.name}
                        className="mt-1 block w-full"
                        autoComplete="name"
                        isFocused={true}
                        onChange={(e) => setData('name', e.target.value)}
                        required
                    />

                    <InputError message={errors.name} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        onChange={(e) => setData('email', e.target.value)}
                        required
                    />

                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(e) => setData('password', e.target.value)}
                        required
                    />

                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel
                        htmlFor="password_confirmation"
                        value="Confirm Password"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        value={data.password_confirmation}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(e) =>
                            setData('password_confirmation', e.target.value)
                        }
                        required
                    />

                    <InputError
                        message={errors.password_confirmation}
                        className="mt-2"
                    />
                </div>

                <div className="mt-4 flex items-center justify-end">
                    <Link
                        href={route('login')}
                        className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Already registered?
                    </Link>

                    <PrimaryButton className="ms-4" disabled={processing}>
                        Register
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
