program heat_sim
    implicit none
    integer, parameter :: nx = 50, ny = 50
    integer, parameter :: steps = 10
    real(8), dimension(nx, ny) :: u, u_new
    real(8) :: alpha = 0.24d0
    integer :: i, j, step
    character(len=255) :: in_file, out_file

    call get_command_argument(1, in_file)
    call get_command_argument(2, out_file)

    open(10, file=trim(in_file), status='old', action='read')
    do i = 1, nx
        read(10, *) u(i, :)
    end do
    close(10)

    u_new = u

    do step = 1, steps
        do j = 2, ny-1
            do i = 2, nx-1
                u_new(i, j) = u(i, j) + alpha * ( &
                    u(i+1, j) + u(i-1, j) + u(i, j+1) + u(i, j-1) - 4.0d0 * u(i, j) &
                )
            end do
        end do
        u = u_new
    end do

    open(20, file=trim(out_file), status='replace', action='write')
    do i = 1, nx
        write(20, '(50(F12.6, 1X))') u(i, :)
    end do
    close(20)
end program heat_sim
