<nav class="navbar-container">
    <div class="navbar-left-content">
        <a class="navbar-home-redirect" href="{{ url('/') }}">
            Multitenancy
        </a>
    </div>
    <div class="navbar-right-content">
        <ul>
            <li>
                <a href="{{ route('users.index') }}">Users</a>
            </li>
            <li>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </li>
        </ul>
    </div>
</nav>
