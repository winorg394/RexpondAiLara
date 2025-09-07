import { dashboard, login, register } from '@/routes';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

type HttpMethod = 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';

interface ApiEndpointProps {
    method: HttpMethod;
    path: string;
    description: string;
    requestBody?: any;
    response?: any;
    requiresAuth?: boolean;
}

interface Endpoint {
    method: HttpMethod;
    path: string;
    description: string;
    requestBody?: any;
    response?: any;
    requiresAuth?: boolean;
}

const ApiEndpoint = ({ method, path, description, requestBody, response, requiresAuth = false }: ApiEndpointProps) => {
  const [isExpanded, setIsExpanded] = useState(false);
  
  const copyToClipboard = async (text: any) => {
    try {
      await navigator.clipboard.writeText(text);
      // Optional: Show a toast or notification
    } catch (err) {
      console.error('Failed to copy:', err);
    }
  };

  return (
    <div className="mb-4 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
      <div 
        className={`flex cursor-pointer items-center justify-between p-4 ${isExpanded ? 'bg-gray-50 dark:bg-gray-800' : 'hover:bg-gray-50 dark:hover:bg-gray-800'}`}
        onClick={() => setIsExpanded(!isExpanded)}
      >
        <div className="flex items-center space-x-4">
          <span className={`inline-flex items-center rounded px-2.5 py-0.5 text-xs font-medium ${
            method === 'GET' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
            method === 'POST' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
          }`}>
            {method}
          </span>
          <code className="font-mono text-sm">{path}</code>
          {requiresAuth && (
            <span className="rounded bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
              Auth Required
            </span>
          )}
        </div>
        <svg
          className={`h-5 w-5 text-gray-500 transition-transform duration-200 ${isExpanded ? 'rotate-180' : ''}`}
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
        </svg>
      </div>
      {isExpanded && (
        <div className="border-t border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
          <p className="mb-4 text-gray-700 dark:text-gray-300">{description}</p>
          
          {requestBody && (
            <div className="mb-4">
              <h4 className="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Request Body:</h4>
              <div className="relative">
                <pre className="overflow-x-auto rounded bg-gray-800 p-4 text-xs text-gray-100">
                  {JSON.stringify(requestBody, null, 2)}
                </pre>
                <button
                  onClick={() => copyToClipboard(JSON.stringify(requestBody, null, 2))}
                  className="absolute right-2 top-2 rounded bg-gray-700 p-1 text-xs text-white hover:bg-gray-600"
                >
                  Copy
                </button>
              </div>
            </div>
          )}
          
          {response && (
            <div>
              <h4 className="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Response:</h4>
              <div className="relative">
                <pre className="overflow-x-auto rounded bg-gray-800 p-4 text-xs text-gray-100">
                  {JSON.stringify(response, null, 2)}
                </pre>
                <button
                  onClick={() => copyToClipboard(JSON.stringify(response, null, 2))}
                  className="absolute right-2 top-2 rounded bg-gray-700 p-1 text-xs text-white hover:bg-gray-600"
                >
                  Copy
                </button>
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  );
};

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;
    const [activeTab, setActiveTab] = useState('auth');

    const authEndpoints: Endpoint[] = [
      {
        method: 'POST',
        path: '/api/register',
        description: 'Register a new user account',
        requestBody: {
          first_name: 'string',
          last_name: 'string',
          email: 'string',
          password: 'string',
          password_confirmation: 'string',
          device_name: 'string'
        },
        response: {
          token: 'string',
          user: {
            id: 'number',
            name: 'string',
            email: 'string',
            email_verified_at: 'datetime',
            created_at: 'datetime',
            updated_at: 'datetime'
          }
        }
      },
      {
        method: 'POST',
        path: '/api/login',
        description: 'Authenticate user and retrieve access token',
        requestBody: {
          email: 'string',
          password: 'string',
          device_name: 'string'
        },
        response: {
          token: 'string',
          user: 'object'
        }
      },
      {
        method: 'POST',
        path: '/api/logout',
        description: 'Revoke the current access token',
        requiresAuth: true,
        response: {
          message: 'Logged out successfully'
        }
      },
      {
        method: 'GET',
        path: '/api/me',
        description: 'Get the authenticated user\'s profile',
        requiresAuth: true,
        response: {
          id: 'number',
          name: 'string',
          email: 'string',
          email_verified_at: 'datetime',
          created_at: 'datetime',
          updated_at: 'datetime'
        }
      },
      {
        method: 'POST',
        path: '/api/auth/login-with-otp',
        description: 'Login with OTP code',
        requestBody: {
          email: 'string',
          otp: 'string',
          device_name: 'string'
        },
        response: {
          token: 'string',
          user: 'object'
        }
      }
    ];

    const otpEndpoints: Endpoint[] = [
      {
        method: 'POST',
        path: '/api/auth/otp/send',
        description: 'Send OTP to user\'s email',
        requestBody: {
          email: 'string'
        },
        response: {
          message: 'OTP sent successfully'
        }
      },
      {
        method: 'POST',
        path: '/api/auth/otp/verify',
        description: 'Verify OTP code',
        requestBody: {
          email: 'string',
          otp: 'string'
        },
        response: {
          message: 'OTP verified successfully',
          verification_token: 'string'
        }
      }
    ];

    const googleEndpoints: Endpoint[] = [
      {
        method: 'GET',
        path: '/api/auth/google/import',
        description: 'Initiate Google OAuth connection',
        requiresAuth: true,
        response: {
          url: 'string (Google OAuth URL)'
        }
      },
      {
        method: 'GET',
        path: '/api/emails/unread',
        description: 'Get unread emails from connected Gmail account',
        requiresAuth: true,
        requestBody: {
          limit: 'number (optional, default: 50)'
        },
        response: {
          emails: 'Email[]',
          total: 'number'
        }
      }
    ];

    return (
        <>
            <Head title="API Documentation">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className="min-h-screen bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a]">
                <header className="mb-8 w-full max-w-7xl mx-auto">
                    <nav className="flex items-center justify-between">
                        <div className="text-2xl font-bold">RexpondAI API Documentation</div>
                        <div>
                            {auth.user ? (
                                <Link
                                    href={dashboard()}
                                    className="rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                                >
                                    Dashboard
                                </Link>
                            ) : (
                                <div className="flex space-x-4">
                                    <Link
                                        href={login()}
                                        className="rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                                    >
                                        Log in
                                    </Link>
                                    <Link
                                        href={register()}
                                        className="rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                                    >
                                        Register
                                    </Link>
                                </div>
                            )}
                        </div>
                    </nav>
                </header>

                <main className="mx-auto max-w-7xl">
                    <div className="mb-8">
                        <h1 className="mb-4 text-3xl font-bold">API Reference</h1>
                        <p className="text-gray-600 dark:text-gray-400">
                            Welcome to the RexpondAI API documentation. This API provides endpoints for user authentication, 
                            OTP verification, and email management.
                        </p>
                    </div>

                    <div className="mb-6 border-b border-gray-200 dark:border-gray-700">
                        <nav className="-mb-px flex space-x-8">
                            <button
                                onClick={() => setActiveTab('auth')}
                                className={`whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium ${
                                    activeTab === 'auth'
                                        ? 'border-[#f53003] text-[#f53003] dark:border-[#FF4433] dark:text-[#FF4433]'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'
                                }`}
                            >
                                Authentication
                            </button>
                            <button
                                onClick={() => setActiveTab('otp')}
                                className={`whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium ${
                                    activeTab === 'otp'
                                        ? 'border-[#f53003] text-[#f53003] dark:border-[#FF4433] dark:text-[#FF4433]'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'
                                }`}
                            >
                                OTP
                            </button>
                            <button
                                onClick={() => setActiveTab('google')}
                                className={`whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium ${
                                    activeTab === 'google'
                                        ? 'border-[#f53003] text-[#f53003] dark:border-[#FF4433] dark:text-[#FF4433]'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'
                                }`}
                            >
                                Google API
                            </button>
                        </nav>
                    </div>

                    <div className="space-y-8">
                        {activeTab === 'auth' && (
                            <div>
                                <h2 className="mb-4 text-xl font-semibold">Authentication</h2>
                                <div className="space-y-4">
                                {authEndpoints.map((endpoint, index) => (
  <ApiEndpoint 
    key={`auth-${index}`}
    method={endpoint.method}
    path={endpoint.path}
    description={endpoint.description}
    requestBody={endpoint.requestBody}  // This is now optional
    response={endpoint.response}
    requiresAuth={endpoint.requiresAuth}
  />
))}
                                </div>
                            </div>
                        )}

                        {activeTab === 'otp' && (
                            <div>
                                <h2 className="mb-4 text-xl font-semibold">OTP Verification</h2>
                                <div className="space-y-4">
                                    {otpEndpoints.map((endpoint, index) => (
                                        <ApiEndpoint key={`otp-${index}`} {...endpoint} />
                                    ))}
                                </div>
                            </div>
                        )}

                        {activeTab === 'google' && (
                            <div>
                                <h2 className="mb-4 text-xl font-semibold">Google API</h2>
                                <div className="space-y-4">
                                    {googleEndpoints.map((endpoint, index) => (
                                        <ApiEndpoint 
                                            key={`google-${index}`}
                                            method={endpoint.method}
                                            path={endpoint.path}
                                            description={endpoint.description}
                                            requestBody={endpoint.requestBody}
                                            response={endpoint.response}
                                            requiresAuth={endpoint.requiresAuth}
                                        />
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                </main>
            </div>
        </>
    );
}
