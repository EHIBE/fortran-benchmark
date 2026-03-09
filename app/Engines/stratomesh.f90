program stratomesh
    implicit none
    integer :: iter
    real(8) :: grid(100, 100), next_grid(100, 100)
    integer, parameter :: n = 100, iters = 500
    real(8), parameter :: diff = 0.2d0
    
    read(*, *) grid
    
    do iter = 1, iters
        next_grid = grid
        next_grid(2:n-1, 2:n-1) = grid(2:n-1, 2:n-1) + diff * &
            (grid(1:n-2, 2:n-1) + grid(3:n, 2:n-1) + &
             grid(2:n-1, 1:n-2) + grid(2:n-1, 3:n) - &
             4.0d0 * grid(2:n-1, 2:n-1))
        grid = next_grid
    end do
    
    write(*, *) grid
end program stratomesh