program nbody_sim
    implicit none
    integer, parameter :: n = 1000
    real(8), dimension(n) :: x, y, vx, vy, mass
    real(8) :: dx, dy, dist, force, dt = 0.01, G = 1.0d0
    real(8) :: centerX = 300.0, centerY = 300.0, centralMass = 500000.0
    integer :: i, j
    character(len=255) :: in_file, out_file

    call get_command_argument(1, in_file)
    call get_command_argument(2, out_file)

    open(10, file=trim(in_file), status='old', action='read')
    do i = 1, n
        read(10, *) x(i), y(i), vx(i), vy(i), mass(i)
    end do
    close(10)

    do i = 1, n
        dx = centerX - x(i)
        dy = centerY - y(i)
        dist = sqrt(dx**2 + dy**2) + 20.0
        force = (G * mass(i) * centralMass) / (dist**2)
        vx(i) = vx(i) + (force * dx / dist) / mass(i) * dt
        vy(i) = vy(i) + (force * dy / dist) / mass(i) * dt

        do j = 1, n
            if (i == j) cycle
            dx = x(j) - x(i)
            dy = y(j) - y(i)
            dist = sqrt(dx**2 + dy**2) + 10.0
            force = (G * mass(i) * mass(j)) / (dist**2)
            vx(i) = vx(i) + (force * dx / dist) / mass(i) * dt
            vy(i) = vy(i) + (force * dy / dist) / mass(i) * dt
        end do
    end do

    x = x + vx * dt
    y = y + vy * dt

    open(20, file=trim(out_file), status='replace', action='write')
    do i = 1, n
        write(20, '(5(F16.8, 1X))') x(i), y(i), vx(i), vy(i), mass(i)
    end do
    close(20)
end program nbody_sim
